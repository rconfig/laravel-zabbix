<?php

namespace Rconfig\Zabbix;

use Rconfig\Zabbix\Contracts\ZabbixClient;
use Rconfig\Zabbix\Exceptions\ZabbixException;
use Rconfig\Zabbix\Http\JsonRpcClient;

class ZabbixConnector
{
    /**
     * Get audit logs resource
     */
    public function auditLogs(): \Rconfig\Zabbix\Resources\AuditLogs
    {
        $this->ensureLoggedIn();
        return new \Rconfig\Zabbix\Resources\AuditLogs($this->client);
    }

    /**
     * Get autoregistrations resource
     */
    public function autoregistrations(): \Rconfig\Zabbix\Resources\Autoregistrations
    {
        $this->ensureLoggedIn();
        return new \Rconfig\Zabbix\Resources\Autoregistrations($this->client);
    }

    /**
     * Get configurations resource
     */
    public function configurations(): \Rconfig\Zabbix\Resources\Configurations
    {
        $this->ensureLoggedIn();
        return new \Rconfig\Zabbix\Resources\Configurations($this->client);
    }

    /**
     * Get correlations resource
     */
    public function correlations(): \Rconfig\Zabbix\Resources\Correlations
    {
        $this->ensureLoggedIn();
        return new \Rconfig\Zabbix\Resources\Correlations($this->client);
    }

    /**
     * Get discovered hosts resource
     */
    public function discoveredHosts(): \Rconfig\Zabbix\Resources\DiscoveredHosts
    {
        $this->ensureLoggedIn();
        return new \Rconfig\Zabbix\Resources\DiscoveredHosts($this->client);
    }

    /**
     * Get discovered services resource
     */
    public function discoveredServices(): \Rconfig\Zabbix\Resources\DiscoveredServices
    {
        $this->ensureLoggedIn();
        return new \Rconfig\Zabbix\Resources\DiscoveredServices($this->client);
    }

    /**
     * Get high availability nodes resource
     */
    public function highAvailabilityNodes(): \Rconfig\Zabbix\Resources\HighAvailabilityNodes
    {
        $this->ensureLoggedIn();
        return new \Rconfig\Zabbix\Resources\HighAvailabilityNodes($this->client);
    }

    /**
     * Get histories resource
     */
    public function histories(): \Rconfig\Zabbix\Resources\Histories
    {
        $this->ensureLoggedIn();
        return new \Rconfig\Zabbix\Resources\Histories($this->client);
    }

    /**
     * Get maintenances resource
     */
    public function maintenances(): \Rconfig\Zabbix\Resources\Maintenances
    {
        $this->ensureLoggedIn();
        return new \Rconfig\Zabbix\Resources\Maintenances($this->client);
    }

    /**
     * Get problems resource
     */
    public function problems(): \Rconfig\Zabbix\Resources\Problems
    {
        $this->ensureLoggedIn();
        return new \Rconfig\Zabbix\Resources\Problems($this->client);
    }

    /**
     * Get tokens resource
     */

    public function tokens(): \Rconfig\Zabbix\Resources\Tokens
    {
        $this->ensureLoggedIn();
        return new \Rconfig\Zabbix\Resources\Tokens($this->client);
    }

    // --- Properties ---
    protected ?ZabbixClient $client = null;
    protected ?string $baseUrl = null;
    protected ?string $endpoint = null;
    protected ?string $username = null;
    protected ?string $password = null;
    protected ?string $token = null;
    protected bool $isLoggedIn = false;
    // Additional options properties
    protected bool $debug = false;
    protected ?string $sslCaFile = null;
    protected int $sslVerifyPeer = 1;
    protected int $sslVerifyHost = 2;
    protected bool $useGzip = true;
    protected int $timeout = 30;
    protected int $connectTimeout = 30;
    protected int $retries = 2;
    protected int $retrySleepMs = 250;

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
        // Apply options first to set up configuration
        $this->applyOptions($options);

        // Get credentials from parameters or fall back to config
        $this->baseUrl = $baseUrl ?? config('zabbix.base_url');
        $this->endpoint = $options['endpoint'] ?? config('zabbix.endpoint', '/api_jsonrpc.php');
        $this->username = $username ?? config('zabbix.username');
        $this->password = $password ?? config('zabbix.password');
        $this->token = $options['token'] ?? config('zabbix.token');

        // FIXED: Clean up the token - treat empty string as null
        if (empty($this->token)) {
            $this->token = null;
        }

        // Validate we have either token or username/password
        if ($this->token === null && (empty($this->username) || empty($this->password))) {
            throw new ZabbixException('No valid credentials provided. Either provide token or username/password, or configure them in config/zabbix.php');
        }

        // Validate base URL
        if (empty($this->baseUrl)) {
            throw new ZabbixException('No Zabbix base URL provided. Either pass it to login() or configure ZABBIX_BASE_URL in your .env file');
        }

        // Clean and prepare the URL (remove endpoints, trailing slashes, etc.)
        $this->baseUrl = $this->prepareUrl($this->baseUrl);

        // Debug output after credentials are set
        if ($this->debug) {
            dump("DBG login(). Using zabUser: " . ($this->username ?? 'N/A') . ", zabUrl: " . $this->baseUrl . "\n");
            dump("DBG login(). Library Version: ZabbixConnector v1.0\n");
        }

        // FIXED: Create the JSON-RPC client with proper token detection
        $hasToken = $this->token !== null;

        $this->client = new JsonRpcClient(
            baseUrl: $this->baseUrl,
            endpoint: $this->endpoint,
            username: $this->username,
            password: $this->password,
            hasBearerToken: $hasToken,
            timeout: $this->timeout,
            retries: $this->retries,
            retrySleepMs: $this->retrySleepMs,
            bearer: $hasToken ? $this->token : null,
            sslOptions: $this->buildSslOptions(),
        );

        // Test the connection by calling apiinfo.version
        try {
            $this->apiVersion();
            $this->isLoggedIn = true;
        } catch (\Exception $e) {
            throw new ZabbixException('Failed to connect to Zabbix API: ' . $e->getMessage(), previous: $e);
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
     * Internal function to apply options from an associative array.
     */
    protected function applyOptions(array $options): void
    {
        $validOptions = [
            'debug',
            'sslCaFile',
            'sslVerifyHost',
            'sslVerifyPeer',
            'useGzip',
            'timeout',
            'connectTimeout',
            'retries',
            'retry_sleep_ms',
            'endpoint',
            'token',
        ];

        foreach ($options as $k => $v) {
            if (! in_array($k, $validOptions)) {
                throw new ZabbixException("Invalid option used. option: $k");
            }
        }

        if (array_key_exists('debug', $options)) {
            $this->debug = $options['debug'] && true;
        }

        if ($this->debug) {
            dump("DBG login(). Using zabUser: {$this->username}, zabUrl: {$this->baseUrl}\n");
            dump("DBG login(). Library Version: ZabbixConnector v1.0\n");
        }

        if (array_key_exists('sslCaFile', $options)) {
            if (! is_file($options['sslCaFile'])) {
                throw new ZabbixException("Error - sslCaFile: {$options['sslCaFile']} is not a valid file");
            }
            $this->sslCaFile = $options['sslCaFile'];
        }

        if (array_key_exists('sslVerifyPeer', $options)) {
            $this->sslVerifyPeer = ($options['sslVerifyPeer']) ? 1 : 0;
        }

        if (array_key_exists('sslVerifyHost', $options)) {
            $this->sslVerifyHost = ($options['sslVerifyHost']) ? 2 : 0;
        }

        if (array_key_exists('useGzip', $options)) {
            $this->useGzip = ($options['useGzip']) ? true : false;
        }

        if (array_key_exists('timeout', $options)) {
            $this->timeout = (intval($options['timeout']) > 0) ? $options['timeout'] : 30;
        }

        if (array_key_exists('connectTimeout', $options)) {
            $this->connectTimeout = (intval($options['connectTimeout']) > 0) ? $options['connectTimeout'] : 30;
        }

        if (array_key_exists('retries', $options)) {
            $this->retries = (intval($options['retries']) >= 0) ? $options['retries'] : 2;
        }

        if (array_key_exists('retry_sleep_ms', $options)) {
            $this->retrySleepMs = (intval($options['retry_sleep_ms']) >= 0) ? $options['retry_sleep_ms'] : 250;
        }

        if ($this->debug) {
            dump("DBG login(). Using sslVerifyPeer: {$this->sslVerifyPeer} sslVerifyHost: {$this->sslVerifyHost} useGzip: {$this->useGzip} timeout: {$this->timeout} connectTimeout: {$this->connectTimeout}\n");
        }
    }

    /**
     * Build SSL options array for the HTTP client
     */
    protected function buildSslOptions(): array
    {
        $sslOptions = [];

        // SSL verification options
        if ($this->sslVerifyPeer === 0) {
            $sslOptions['verify'] = false;
        }

        if ($this->sslVerifyHost === 0) {
            $sslOptions['verify'] = false;
        }

        // SSL CA file
        if ($this->sslCaFile) {
            $sslOptions['cafile'] = $this->sslCaFile;
        }

        // Connect timeout
        $sslOptions['connect_timeout'] = $this->connectTimeout;

        return $sslOptions;
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
    public function hosts(): \Rconfig\Zabbix\Resources\Hosts
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Hosts($this->client);
    }

    /**
     * Get host groups resource
     */
    public function hostGroups(): \Rconfig\Zabbix\Resources\HostGroups
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\HostGroups($this->client);
    }

    /**
     * Get items resource
     */
    public function items(): \Rconfig\Zabbix\Resources\Items
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Items($this->client);
    }

    /**
     * Get templates resource
     */
    public function templates(): \Rconfig\Zabbix\Resources\Templates
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Templates($this->client);
    }

    /**
     * Get triggers resource
     */
    public function triggers(): \Rconfig\Zabbix\Resources\Triggers
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Triggers($this->client);
    }

    /**
     * Get users resource
     */
    public function users(): \Rconfig\Zabbix\Resources\Users
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Users($this->client);
    }

    /**
     * Get user groups resource
     */
    public function userGroups(): \Rconfig\Zabbix\Resources\UserGroups
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\UserGroups($this->client);
    }

    /**
     * Get actions resource
     */
    public function actions(): \Rconfig\Zabbix\Resources\Actions
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Actions($this->client);
    }

    /**
     * Get alerts resource
     */
    public function alerts(): \Rconfig\Zabbix\Resources\Alerts
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Alerts($this->client);
    }

    /**
     * Get authentication resource
     */
    public function authentication(): \Rconfig\Zabbix\Resources\Authentication
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Authentication($this->client);
    }

    /**
     * Get auto registration resource
     */
    public function autoRegistration(): \Rconfig\Zabbix\Resources\AutoRegistration
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\AutoRegistration($this->client);
    }

    /**
     * Get configuration resource
     */
    public function configuration(): \Rconfig\Zabbix\Resources\Configuration
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Configuration($this->client);
    }

    /**
     * Get connectors resource
     */
    public function connectors(): \Rconfig\Zabbix\Resources\Connectors
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Connectors($this->client);
    }

    /**
     * Get correlation resource
     */
    public function correlation(): \Rconfig\Zabbix\Resources\Correlation
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Correlation($this->client);
    }

    /**
     * Get dashboards resource
     */
    public function dashboards(): \Rconfig\Zabbix\Resources\Dashboards
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Dashboards($this->client);
    }

    /**
     * Get discovery checks resource
     */
    public function discoveryChecks(): \Rconfig\Zabbix\Resources\DiscoveryChecks
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\DiscoveryChecks($this->client);
    }

    /**
     * Get discovery rules resource
     */
    public function discoveryRules(): \Rconfig\Zabbix\Resources\DiscoveryRules
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\DiscoveryRules($this->client);
    }

    /**
     * Get events resource
     */
    public function events(): \Rconfig\Zabbix\Resources\Events
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Events($this->client);
    }

    /**
     * Get graphs resource
     */
    public function graphs(): \Rconfig\Zabbix\Resources\Graphs
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Graphs($this->client);
    }

    /**
     * Get graph items resource
     */
    public function graphItems(): \Rconfig\Zabbix\Resources\GraphItems
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\GraphItems($this->client);
    }

    /**
     * Get graph prototypes resource
     */
    public function graphPrototypes(): \Rconfig\Zabbix\Resources\GraphPrototypes
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\GraphPrototypes($this->client);
    }

    /**
     * GetHA nodes resource
     */
    public function haNodes(): \Rconfig\Zabbix\Resources\HANodes
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\HANodes($this->client);
    }

    /**
     * Get history resource
     */
    public function history(): \Rconfig\Zabbix\Resources\History
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\History($this->client);
    }

    /**
     * Get host interfaces resource
     */
    public function hostInterfaces(): \Rconfig\Zabbix\Resources\HostInterfaces
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\HostInterfaces($this->client);
    }

    /**
     * Get host prototypes resource
     */
    public function hostPrototypes(): \Rconfig\Zabbix\Resources\HostPrototypes
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\HostPrototypes($this->client);
    }

    /**
     * Get housekeeping resource
     */
    public function housekeeping(): \Rconfig\Zabbix\Resources\Housekeeping
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Housekeeping($this->client);
    }

    /**
     * Get icon maps resource
     */
    public function iconMaps(): \Rconfig\Zabbix\Resources\IconMaps
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\IconMaps($this->client);
    }

    /**
     * Get images resource
     */
    public function images(): \Rconfig\Zabbix\Resources\Images
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Images($this->client);
    }

    /**
     * Get item prototypes resource
     */
    public function itemPrototypes(): \Rconfig\Zabbix\Resources\ItemPrototypes
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\ItemPrototypes($this->client);
    }

    /**
     * Get LLD rules resource
     */
    public function lldRules(): \Rconfig\Zabbix\Resources\LLDRules
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\LLDRules($this->client);
    }

    /**
     * Get maintenance resource
     */
    public function maintenance(): \Rconfig\Zabbix\Resources\Maintenance
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Maintenance($this->client);
    }

    /**
     * Get maps resource
     */
    public function maps(): \Rconfig\Zabbix\Resources\Maps
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Maps($this->client);
    }

    /**
     * Get media types resource
     */
    public function mediaTypes(): \Rconfig\Zabbix\Resources\MediaTypes
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\MediaTypes($this->client);
    }

    /**
     * Get MFAs resource
     */
    public function mfas(): \Rconfig\Zabbix\Resources\MFAs
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\MFAs($this->client);
    }

    /**
     * Get modules resource
     */
    public function modules(): \Rconfig\Zabbix\Resources\Modules
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Modules($this->client);
    }

    /**
     * Get proxies resource
     */
    public function proxies(): \Rconfig\Zabbix\Resources\Proxies
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Proxies($this->client);
    }

    /**
     * Get proxy groups resource
     */
    public function proxyGroups(): \Rconfig\Zabbix\Resources\ProxyGroups
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\ProxyGroups($this->client);
    }

    /**
     * Get regular expressions resource
     */
    public function regularExpressions(): \Rconfig\Zabbix\Resources\RegularExpressions
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\RegularExpressions($this->client);
    }

    /**
     * Get reports resource
     */
    public function reports(): \Rconfig\Zabbix\Resources\Reports
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Reports($this->client);
    }

    /**
     * Get roles resource
     */
    public function roles(): \Rconfig\Zabbix\Resources\Roles
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Roles($this->client);
    }

    /**
     * Get scripts resource
     */
    public function scripts(): \Rconfig\Zabbix\Resources\Scripts
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Scripts($this->client);
    }

    /**
     * Get services resource
     */
    public function services(): \Rconfig\Zabbix\Resources\Services
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Services($this->client);
    }

    /**
     * Get settings resource
     */
    public function settings(): \Rconfig\Zabbix\Resources\Settings
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Settings($this->client);
    }

    /**
     * Get SLAs resource
     */
    public function slas(): \Rconfig\Zabbix\Resources\SLAs
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\SLAs($this->client);
    }

    /**
     * Get tasks resource
     */
    public function tasks(): \Rconfig\Zabbix\Resources\Tasks
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Tasks($this->client);
    }

    /**
     * Get template dashboards resource
     */
    public function templateDashboards(): \Rconfig\Zabbix\Resources\TemplateDashboards
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\TemplateDashboards($this->client);
    }

    /**
     * Get template groups resource
     */
    public function templateGroups(): \Rconfig\Zabbix\Resources\TemplateGroups
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\TemplateGroups($this->client);
    }

    /**
     * Get trends resource
     */
    public function trends(): \Rconfig\Zabbix\Resources\Trends
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\Trends($this->client);
    }

    /**
     * Get trigger prototypes resource
     */
    public function triggerPrototypes(): \Rconfig\Zabbix\Resources\TriggerPrototypes
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\TriggerPrototypes($this->client);
    }

    /**
     * Get user directories resource
     */
    public function userDirectories(): \Rconfig\Zabbix\Resources\UserDirectories
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\UserDirectories($this->client);
    }

    /**
     * Get user macros resource
     */
    public function userMacros(): \Rconfig\Zabbix\Resources\UserMacros
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\UserMacros($this->client);
    }

    /**
     * Get value maps resource
     */
    public function valueMaps(): \Rconfig\Zabbix\Resources\ValueMaps
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\ValueMaps($this->client);
    }

    /**
     * Get web scenarios resource
     */
    public function webScenarios(): \Rconfig\Zabbix\Resources\WebScenarios
    {
        $this->ensureLoggedIn();

        return new \Rconfig\Zabbix\Resources\WebScenarios($this->client);
    }

    /**
     * Get current debug status
     */
    public function isDebugEnabled(): bool
    {
        return $this->debug;
    }

    /**
     * Get current timeout setting
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Get current connect timeout setting
     */
    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    /**
     * Get current SSL verification settings
     */
    public function getSslSettings(): array
    {
        return [
            'sslVerifyPeer' => $this->sslVerifyPeer,
            'sslVerifyHost' => $this->sslVerifyHost,
            'sslCaFile' => $this->sslCaFile,
        ];
    }

    /**
     * Get current compression setting
     */
    public function isGzipEnabled(): bool
    {
        return $this->useGzip;
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

    /**
     * Clean and prepare the URL for Zabbix API
     * Removes common Zabbix API endpoints and trailing slashes
     */
    private function prepareUrl(string $url): string
    {
        // Remove trailing slash
        $url = rtrim($url, '/');

        // Remove common Zabbix API endpoints if they exist
        $endpoints = [
            '/api_jsonrpc.php',
            '/zabbix/api_jsonrpc.php',
            '/frontend/api_jsonrpc.php',  // Some custom installations
            '/web/api_jsonrpc.php',       // Some custom installations
        ];

        foreach ($endpoints as $endpoint) {
            if (str_ends_with($url, $endpoint)) {
                $url = str_replace($endpoint, '', $url);
                break;
            }
        }

        return $url;
    }
}
