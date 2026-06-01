<?php

namespace Rconfig\Zabbix\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Rconfig\Zabbix\Contracts\ZabbixClient;
use Rconfig\Zabbix\Exceptions\ZabbixException;
use Rconfig\Zabbix\Exceptions\ZabbixHttpException;

class JsonRpcClient implements ZabbixClient
{
    protected ?string $authToken = null;

    protected int $id = 1;

    protected ?string $apiVersion = null;

    protected ?bool $useAuthHeader = null; // null = not yet determined

    protected ?string $discoveredEndpoint = null; // The working endpoint we discovered

    public function __construct(
        protected string $baseUrl,
        protected string $endpoint,
        protected ?string $username = null,
        protected ?string $password = null,
        protected bool $hasBearerToken = false,
        protected int $timeout = 15,
        protected int $retries = 2,
        protected int $retrySleepMs = 250,
        protected ?string $bearer = null,
        protected array $sslOptions = [],
    ) {
        // Don't normalize in constructor - we'll discover the correct endpoint
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function call(string $method, array $params): mixed
    {
        // Ensure we know the API version and have discovered the endpoint
        if ($this->useAuthHeader === null && $method !== 'apiinfo.version') {
            $this->detectApiVersion();
        }

        $body = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => $this->id++,
        ];

        // Determine if we need auth (and which method)
        $needsAuth = $this->needsAuthentication($method);

        if ($needsAuth) {
            $this->ensureSessionToken($method);

            // Only add auth to body if we're NOT using header auth
            if (! $this->useAuthHeader) {
                $body['auth'] = $this->authToken;
            }
        }

        try {
            $res = $this->request($method, $needsAuth)->post($this->getFullEndpointUrl(), $body)->throw();
        } catch (RequestException $e) {
            throw new ZabbixHttpException($e->getMessage(), previous: $e);
        }

        $data = $res->json();

        if (isset($data['error'])) {
            throw new ZabbixException(
                sprintf('[%s] %s', $data['error']['code'] ?? 'ERR', $data['error']['data'] ?? $data['error']['message'] ?? 'Unknown')
            );
        }

        return $data['result'] ?? $data;
    }

    /**
     * Discover the correct Zabbix API endpoint by trying multiple variations
     *
     * @return string The working endpoint URL
     *
     * @throws ZabbixException
     */
    protected function discoverEndpoint(): string
    {
        if ($this->discoveredEndpoint) {
            return $this->discoveredEndpoint;
        }

        // Build list of possible endpoint URLs to try
        $possibleEndpoints = $this->buildEndpointVariations();

        $versionBody = [
            'jsonrpc' => '2.0',
            'method' => 'apiinfo.version',
            'params' => [],
            'id' => $this->id++,
        ];

        $lastError = null;

        foreach ($possibleEndpoints as $testUrl) {
            try {
                $response = Http::timeout(5)
                    ->withOptions($this->sslOptions)
                    ->post($testUrl, $versionBody);

                // Check if we got a valid response
                if ($response->successful()) {
                    $data = $response->json();

                    // Valid Zabbix API response has jsonrpc and result/error
                    if (isset($data['jsonrpc']) && (isset($data['result']) || isset($data['error']))) {
                        $this->discoveredEndpoint = $testUrl;

                        return $testUrl;
                    }
                }
            } catch (\Exception $e) {
                $lastError = $e->getMessage();

                // Continue to next endpoint
                continue;
            }
        }

        // If we get here, none of the endpoints worked
        $triedUrls = implode("\n   - ", $possibleEndpoints);
        throw new ZabbixException(
            "Could not discover Zabbix API endpoint. Tried the following URLs:\n   - {$triedUrls}\n\nLast error: {$lastError}\n\nPlease verify:\n".
                "1. The Zabbix URL is correct\n".
                "2. The server is accessible\n".
                '3. SSL certificates are valid (or disable SSL verification)'
        );
    }

    /**
     * Build a list of possible endpoint variations to try
     *
     * @return array List of full endpoint URLs to test
     */
    protected function buildEndpointVariations(): array
    {
        $variations = [];
        $base = $this->baseUrl;

        // Parse the URL to understand what we're working with
        $parsed = parse_url($base);
        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';
        $port = isset($parsed['port']) ? ":{$parsed['port']}" : '';
        $path = $parsed['path'] ?? '';

        // Clean up the path
        $path = '/'.trim($path, '/');
        if ($path === '/') {
            $path = '';
        }

        $baseWithoutPath = "{$scheme}://{$host}{$port}";

        // Strategy: Try most common modern endpoints first, then fall back to legacy

        // 1. If user provided full path to api_jsonrpc.php, try that first
        if (str_ends_with($base, 'api_jsonrpc.php')) {
            $variations[] = $base;
        }

        // 2. Modern Zabbix 7.2+ style: /api_jsonrpc.php at root (no /zabbix/)
        if (! str_contains($path, '/zabbix')) {
            $variations[] = $baseWithoutPath.$path.'/api_jsonrpc.php';
        }

        // 3. Traditional: /zabbix/api_jsonrpc.php
        $variations[] = $baseWithoutPath.'/zabbix/api_jsonrpc.php';

        // 4. If user gave us a path other than /zabbix, try that with /api_jsonrpc.php
        if ($path && $path !== '/zabbix') {
            $variations[] = $baseWithoutPath.$path.'/api_jsonrpc.php';
        }

        // 5. Just /zabbix path on its own (some setups use this as the endpoint)
        $variations[] = $baseWithoutPath.'/zabbix';

        // 6. Just in case: root level
        $variations[] = $baseWithoutPath.'/api_jsonrpc.php';

        // 7. Custom path + /zabbix + endpoint (for subdirectory installs)
        if ($path && $path !== '/zabbix') {
            $variations[] = $baseWithoutPath.$path.'/zabbix/api_jsonrpc.php';
        }

        // 8. Custom path + /zabbix on its own
        if ($path && $path !== '/zabbix') {
            $variations[] = $baseWithoutPath.$path.'/zabbix';
        }

        // Remove duplicates while preserving order
        return array_values(array_unique($variations));
    }

    protected function detectApiVersion(): void
    {
        try {
            // First discover the working endpoint
            $workingEndpoint = $this->discoverEndpoint();

            // Now get the version
            $versionBody = [
                'jsonrpc' => '2.0',
                'method' => 'apiinfo.version',
                'params' => [],
                'id' => $this->id++,
            ];

            $res = Http::timeout($this->timeout)
                ->withOptions($this->sslOptions)
                ->post($workingEndpoint, $versionBody)
                ->throw()
                ->json();

            $this->apiVersion = $res['result'] ?? null;

            if (! $this->apiVersion) {
                throw new ZabbixException('Failed to retrieve API version from Zabbix');
            }

            // Zabbix 6.4+ prefers Authorization header, 7.2+ requires it
            $this->useAuthHeader = version_compare($this->apiVersion, '6.4.0', '>=');
        } catch (ZabbixException $e) {
            // Re-throw our own exceptions
            throw $e;
        } catch (\Exception $e) {
            throw new ZabbixException(
                'Failed to detect Zabbix API version: '.$e->getMessage()
            );
        }
    }

    protected function request(string $method, bool $needsAuth = false): PendingRequest
    {
        $req = Http::timeout($this->timeout)
            ->retry($this->retries, $this->retrySleepMs);

        // Apply SSL options if provided
        if (! empty($this->sslOptions)) {
            $req = $req->withOptions($this->sslOptions);
        }

        // Send Authorization header if:
        // 1. Using bearer token auth, OR
        // 2. Using legacy auth but API version requires header
        if ($needsAuth) {
            if ($this->hasBearerToken && $this->bearer) {
                $req = $req->withToken($this->bearer);
            } elseif ($this->useAuthHeader && $this->authToken) {
                $req = $req->withToken($this->authToken);
            }
        }

        return $req;
    }

    protected function needsAuthentication(string $method): bool
    {
        return $method !== 'user.login' && $method !== 'apiinfo.version';
    }

    protected function ensureSessionToken(string $method): void
    {
        // If we already have a token or we're using bearer token, we're good
        if ($this->authToken || $this->hasBearerToken) {
            return;
        }

        // If we don't have credentials, we can't login
        if (! $this->username || ! $this->password) {
            throw new ZabbixException('Missing legacy credentials for authentication.');
        }

        // Determine which parameter name to use based on API version
        // Zabbix 5.4+ uses 'username', earlier versions use 'user'
        $userParam = 'username';
        if ($this->apiVersion && version_compare($this->apiVersion, '5.4.0', '<')) {
            $userParam = 'user';
        }

        // Perform the login to get a session token
        $login = [
            'jsonrpc' => '2.0',
            'method' => 'user.login',
            'params' => [$userParam => $this->username, 'password' => $this->password],
            'id' => $this->id++,
        ];

        try {
            // user.login call should NOT carry Authorization header
            $res = $this->request($method, false)->post($this->getFullEndpointUrl(), $login)->throw()->json();

            if (! isset($res['result'])) {
                $errorMsg = isset($res['error'])
                    ? sprintf('[%s] %s', $res['error']['code'] ?? 'ERR', $res['error']['data'] ?? $res['error']['message'] ?? 'Unknown')
                    : 'No result returned from login';
                throw new ZabbixException('Unable to authenticate with legacy credentials: '.$errorMsg);
            }

            $this->authToken = $res['result'];
        } catch (RequestException $e) {
            throw new ZabbixException(
                'Unable to authenticate with legacy credentials. HTTP Error: '.$e->getMessage()
            );
        }
    }

    /**
     * Get the detected API version
     */
    public function getApiVersion(): ?string
    {
        if ($this->useAuthHeader === null) {
            $this->detectApiVersion();
        }

        return $this->apiVersion;
    }

    /**
     * Get the full API endpoint URL (after discovery)
     */
    public function getFullEndpointUrl(): string
    {
        if (! $this->discoveredEndpoint) {
            $this->discoverEndpoint();
        }

        return $this->discoveredEndpoint;
    }

    /**
     * Get information about the discovered endpoint (for debugging)
     */
    public function getEndpointInfo(): array
    {
        return [
            'provided_url' => $this->baseUrl,
            'discovered_endpoint' => $this->discoveredEndpoint,
            'api_version' => $this->apiVersion,
            'uses_header_auth' => $this->useAuthHeader,
        ];
    }
}
