<?php

namespace Rconfig\Zabbix;

use Rconfig\Zabbix\Contracts\ZabbixClient;
use Rconfig\Zabbix\Exceptions\ZabbixException;
use Rconfig\Zabbix\Http\JsonRpcClient;
use Rconfig\Zabbix\Resources\HostGroups;
use Rconfig\Zabbix\Resources\Hosts;
use Rconfig\Zabbix\Resources\Items;
use Rconfig\Zabbix\Resources\Maintenances;
use Rconfig\Zabbix\Resources\Problems;
use Rconfig\Zabbix\Resources\Templates;

class ZabbixApi
{
    protected ?ZabbixClient $client = null;

    protected ?string $baseUrl = null;

    protected ?string $endpoint = null;

    protected ?string $username = null;

    protected ?string $password = null;

    protected ?string $token = null;

    protected bool $isLoggedIn = false;

    /**
     * Login - setup internal structure and credentials for future API calls
     *
     * @param  string|null  $baseUrl  - Zabbix base URL (if null, uses config)
     * @param  string|null  $username  - Zabbix username (if null, uses config)
     * @param  string|null  $password  - Zabbix password (if null, uses config)
     * @param  array  $options  - optional settings like token, endpoint, timeout, etc.
     *
     * @throws ZabbixException
     */
    public function login(?string $baseUrl = null, ?string $username = null, ?string $password = null, array $options = []): self
    {
        // Get credentials from parameters or fall back to config
        $this->baseUrl = $baseUrl ?? config('zabbix.base_url');
        $this->endpoint = $options['endpoint'] ?? config('zabbix.endpoint', '/api_jsonrpc.php');
        $this->username = $username ?? config('zabbix.username');
        $this->password = $password ?? config('zabbix.password');
        $this->token = $options['token'] ?? config('zabbix.token');

        // Validate we have either token or username/password
        if (empty($this->token) && (empty($this->username) || empty($this->password))) {
            throw new ZabbixException('No valid credentials provided. Either provide token or username/password, or configure them in config/zabbix.php');
        }

        // Validate base URL
        if (empty($this->baseUrl)) {
            throw new ZabbixException('No Zabbix base URL provided. Either pass it to login() or configure ZABBIX_BASE_URL in your .env file');
        }

        // Ensure URL doesn't end with slash for consistency
        $this->baseUrl = rtrim($this->baseUrl, '/');

        // Create the JSON-RPC client
        $hasToken = ! empty($this->token);

        $this->client = new JsonRpcClient(
            baseUrl: $this->baseUrl,
            endpoint: $this->endpoint,
            username: $this->username,
            password: $this->password,
            hasBearerToken: $hasToken,
            timeout: (int) ($options['timeout'] ?? config('zabbix.timeout', 15)),
            retries: (int) ($options['retries'] ?? config('zabbix.retries', 2)),
            retrySleepMs: (int) ($options['retry_sleep_ms'] ?? config('zabbix.retry_sleep_ms', 250)),
            bearer: $hasToken ? $this->token : null,
        );

        // Test the connection by calling apiinfo.version
        try {
            $this->apiVersion();
            $this->isLoggedIn = true;
        } catch (\Exception $e) {
            throw new ZabbixException('Failed to connect to Zabbix API: '.$e->getMessage(), previous: $e);
        }

        return $this;
    }

    /**
     * Alternative login method using a token
     */
    public function loginWithToken(string $baseUrl, string $token, array $options = []): self
    {
        $options['token'] = $token;

        return $this->login($baseUrl, null, null, $options);
    }

    /**
     * Check if we're logged in
     */
    public function isLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    /**
     * Ensure we're logged in before making API calls
     */
    protected function ensureLoggedIn(): void
    {
        if (! $this->isLoggedIn()) {
            throw new ZabbixException('Not logged in. Call login() first.');
        }
    }

    /**
     * Get API version
     */
    public function apiVersion(): string
    {
        if (! $this->client) {
            throw new ZabbixException('Not logged in. Call login() first.');
        }

        /** @var string $version */
        $version = $this->client->call('apiinfo.version', []);

        return $version;
    }

    /**
     * Make a direct API call
     */
    public function call(string $method, array $params = []): mixed
    {
        $this->ensureLoggedIn();

        return $this->client->call($method, $params);
    }

    /**
     * Get hosts resource
     */
    public function hosts(): Hosts
    {
        $this->ensureLoggedIn();

        return new Hosts($this->client);
    }

    /**
     * Get host groups resource
     */
    public function hostGroups(): HostGroups
    {
        $this->ensureLoggedIn();

        return new HostGroups($this->client);
    }

    /**
     * Get items resource
     */
    public function items(): Items
    {
        $this->ensureLoggedIn();

        return new Items($this->client);
    }

    /**
     * Get maintenances resource
     */
    public function maintenances(): Maintenances
    {
        $this->ensureLoggedIn();

        return new Maintenances($this->client);
    }

    /**
     * Get problems resource
     */
    public function problems(): Problems
    {
        $this->ensureLoggedIn();

        return new Problems($this->client);
    }

    /**
     * Get templates resource
     */
    public function templates(): Templates
    {
        $this->ensureLoggedIn();

        return new Templates($this->client);
    }

    /**
     * Logout and clear the session
     */
    public function logout(): void
    {
        $this->client = null;
        $this->isLoggedIn = false;
        $this->baseUrl = null;
        $this->username = null;
        $this->password = null;
        $this->token = null;
    }
}
