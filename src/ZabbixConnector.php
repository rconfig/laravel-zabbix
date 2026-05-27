<?php

namespace Rconfig\Zabbix;

use Rconfig\Zabbix\Contracts\ZabbixClient;
use Rconfig\Zabbix\Exceptions\ZabbixException;
use Rconfig\Zabbix\Http\JsonRpcClient;
use Rconfig\Zabbix\Resources\Actions;
use Rconfig\Zabbix\Resources\Alerts;
use Rconfig\Zabbix\Resources\AuditLogs;
use Rconfig\Zabbix\Resources\Authentication;
use Rconfig\Zabbix\Resources\AutoRegistration;
use Rconfig\Zabbix\Resources\Autoregistrations;
use Rconfig\Zabbix\Resources\Configuration;
use Rconfig\Zabbix\Resources\Configurations;
use Rconfig\Zabbix\Resources\Connectors;
use Rconfig\Zabbix\Resources\Correlation;
use Rconfig\Zabbix\Resources\Correlations;
use Rconfig\Zabbix\Resources\Dashboards;
use Rconfig\Zabbix\Resources\DiscoveredHosts;
use Rconfig\Zabbix\Resources\DiscoveredServices;
use Rconfig\Zabbix\Resources\DiscoveryChecks;
use Rconfig\Zabbix\Resources\DiscoveryRules;
use Rconfig\Zabbix\Resources\Events;
use Rconfig\Zabbix\Resources\GraphItems;
use Rconfig\Zabbix\Resources\GraphPrototypes;
use Rconfig\Zabbix\Resources\Graphs;
use Rconfig\Zabbix\Resources\HANodes;
use Rconfig\Zabbix\Resources\HighAvailabilityNodes;
use Rconfig\Zabbix\Resources\Histories;
use Rconfig\Zabbix\Resources\History;
use Rconfig\Zabbix\Resources\HostGroups;
use Rconfig\Zabbix\Resources\HostInterfaces;
use Rconfig\Zabbix\Resources\HostPrototypes;
use Rconfig\Zabbix\Resources\Hosts;
use Rconfig\Zabbix\Resources\Housekeeping;
use Rconfig\Zabbix\Resources\IconMaps;
use Rconfig\Zabbix\Resources\Images;
use Rconfig\Zabbix\Resources\ItemPrototypes;
use Rconfig\Zabbix\Resources\Items;
use Rconfig\Zabbix\Resources\LLDRules;
use Rconfig\Zabbix\Resources\Maintenance;
use Rconfig\Zabbix\Resources\Maintenances;
use Rconfig\Zabbix\Resources\Maps;
use Rconfig\Zabbix\Resources\MediaTypes;
use Rconfig\Zabbix\Resources\MFAs;
use Rconfig\Zabbix\Resources\Modules;
use Rconfig\Zabbix\Resources\Problems;
use Rconfig\Zabbix\Resources\Proxies;
use Rconfig\Zabbix\Resources\ProxyGroups;
use Rconfig\Zabbix\Resources\RegularExpressions;
use Rconfig\Zabbix\Resources\Reports;
use Rconfig\Zabbix\Resources\Roles;
use Rconfig\Zabbix\Resources\Scripts;
use Rconfig\Zabbix\Resources\Services;
use Rconfig\Zabbix\Resources\Settings;
use Rconfig\Zabbix\Resources\SLAs;
use Rconfig\Zabbix\Resources\Tasks;
use Rconfig\Zabbix\Resources\TemplateDashboards;
use Rconfig\Zabbix\Resources\TemplateGroups;
use Rconfig\Zabbix\Resources\Templates;
use Rconfig\Zabbix\Resources\Tokens;
use Rconfig\Zabbix\Resources\Trends;
use Rconfig\Zabbix\Resources\TriggerPrototypes;
use Rconfig\Zabbix\Resources\Triggers;
use Rconfig\Zabbix\Resources\UserDirectories;
use Rconfig\Zabbix\Resources\UserGroups;
use Rconfig\Zabbix\Resources\UserMacros;
use Rconfig\Zabbix\Resources\Users;
use Rconfig\Zabbix\Resources\ValueMaps;
use Rconfig\Zabbix\Resources\WebScenarios;

class ZabbixConnector
{
    /**
     * Get audit logs resource
     */
    public function auditLogs(): AuditLogs
    {
        $this->ensureLoggedIn();

        return new AuditLogs($this->client);
    }

    /**
     * Get autoregistrations resource
     */
    public function autoregistrations(): Autoregistrations
    {
        $this->ensureLoggedIn();

        return new Autoregistrations($this->client);
    }

    /**
     * Get configurations resource
     */
    public function configurations(): Configurations
    {
        $this->ensureLoggedIn();

        return new Configurations($this->client);
    }

    /**
     * Get correlations resource
     */
    public function correlations(): Correlations
    {
        $this->ensureLoggedIn();

        return new Correlations($this->client);
    }

    /**
     * Get discovered hosts resource
     */
    public function discoveredHosts(): DiscoveredHosts
    {
        $this->ensureLoggedIn();

        return new DiscoveredHosts($this->client);
    }

    /**
     * Get discovered services resource
     */
    public function discoveredServices(): DiscoveredServices
    {
        $this->ensureLoggedIn();

        return new DiscoveredServices($this->client);
    }

    /**
     * Get high availability nodes resource
     */
    public function highAvailabilityNodes(): HighAvailabilityNodes
    {
        $this->ensureLoggedIn();

        return new HighAvailabilityNodes($this->client);
    }

    /**
     * Get histories resource
     */
    public function histories(): Histories
    {
        $this->ensureLoggedIn();

        return new Histories($this->client);
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
     * Get tokens resource
     */
    public function tokens(): Tokens
    {
        $this->ensureLoggedIn();

        return new Tokens($this->client);
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
            dump('DBG login(). Using zabUser: '.($this->username ?? 'N/A').', zabUrl: '.$this->baseUrl."\n");
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
     * Get templates resource
     */
    public function templates(): Templates
    {
        $this->ensureLoggedIn();

        return new Templates($this->client);
    }

    /**
     * Get triggers resource
     */
    public function triggers(): Triggers
    {
        $this->ensureLoggedIn();

        return new Triggers($this->client);
    }

    /**
     * Get users resource
     */
    public function users(): Users
    {
        $this->ensureLoggedIn();

        return new Users($this->client);
    }

    /**
     * Get user groups resource
     */
    public function userGroups(): UserGroups
    {
        $this->ensureLoggedIn();

        return new UserGroups($this->client);
    }

    /**
     * Get actions resource
     */
    public function actions(): Actions
    {
        $this->ensureLoggedIn();

        return new Actions($this->client);
    }

    /**
     * Get alerts resource
     */
    public function alerts(): Alerts
    {
        $this->ensureLoggedIn();

        return new Alerts($this->client);
    }

    /**
     * Get authentication resource
     */
    public function authentication(): Authentication
    {
        $this->ensureLoggedIn();

        return new Authentication($this->client);
    }

    /**
     * Get auto registration resource
     */
    public function autoRegistration(): AutoRegistration
    {
        $this->ensureLoggedIn();

        return new AutoRegistration($this->client);
    }

    /**
     * Get configuration resource
     */
    public function configuration(): Configuration
    {
        $this->ensureLoggedIn();

        return new Configuration($this->client);
    }

    /**
     * Get connectors resource
     */
    public function connectors(): Connectors
    {
        $this->ensureLoggedIn();

        return new Connectors($this->client);
    }

    /**
     * Get correlation resource
     */
    public function correlation(): Correlation
    {
        $this->ensureLoggedIn();

        return new Correlation($this->client);
    }

    /**
     * Get dashboards resource
     */
    public function dashboards(): Dashboards
    {
        $this->ensureLoggedIn();

        return new Dashboards($this->client);
    }

    /**
     * Get discovery checks resource
     */
    public function discoveryChecks(): DiscoveryChecks
    {
        $this->ensureLoggedIn();

        return new DiscoveryChecks($this->client);
    }

    /**
     * Get discovery rules resource
     */
    public function discoveryRules(): DiscoveryRules
    {
        $this->ensureLoggedIn();

        return new DiscoveryRules($this->client);
    }

    /**
     * Get events resource
     */
    public function events(): Events
    {
        $this->ensureLoggedIn();

        return new Events($this->client);
    }

    /**
     * Get graphs resource
     */
    public function graphs(): Graphs
    {
        $this->ensureLoggedIn();

        return new Graphs($this->client);
    }

    /**
     * Get graph items resource
     */
    public function graphItems(): GraphItems
    {
        $this->ensureLoggedIn();

        return new GraphItems($this->client);
    }

    /**
     * Get graph prototypes resource
     */
    public function graphPrototypes(): GraphPrototypes
    {
        $this->ensureLoggedIn();

        return new GraphPrototypes($this->client);
    }

    /**
     * GetHA nodes resource
     */
    public function haNodes(): HANodes
    {
        $this->ensureLoggedIn();

        return new HANodes($this->client);
    }

    /**
     * Get history resource
     */
    public function history(): History
    {
        $this->ensureLoggedIn();

        return new History($this->client);
    }

    /**
     * Get host interfaces resource
     */
    public function hostInterfaces(): HostInterfaces
    {
        $this->ensureLoggedIn();

        return new HostInterfaces($this->client);
    }

    /**
     * Get host prototypes resource
     */
    public function hostPrototypes(): HostPrototypes
    {
        $this->ensureLoggedIn();

        return new HostPrototypes($this->client);
    }

    /**
     * Get housekeeping resource
     */
    public function housekeeping(): Housekeeping
    {
        $this->ensureLoggedIn();

        return new Housekeeping($this->client);
    }

    /**
     * Get icon maps resource
     */
    public function iconMaps(): IconMaps
    {
        $this->ensureLoggedIn();

        return new IconMaps($this->client);
    }

    /**
     * Get images resource
     */
    public function images(): Images
    {
        $this->ensureLoggedIn();

        return new Images($this->client);
    }

    /**
     * Get item prototypes resource
     */
    public function itemPrototypes(): ItemPrototypes
    {
        $this->ensureLoggedIn();

        return new ItemPrototypes($this->client);
    }

    /**
     * Get LLD rules resource
     */
    public function lldRules(): LLDRules
    {
        $this->ensureLoggedIn();

        return new LLDRules($this->client);
    }

    /**
     * Get maintenance resource
     */
    public function maintenance(): Maintenance
    {
        $this->ensureLoggedIn();

        return new Maintenance($this->client);
    }

    /**
     * Get maps resource
     */
    public function maps(): Maps
    {
        $this->ensureLoggedIn();

        return new Maps($this->client);
    }

    /**
     * Get media types resource
     */
    public function mediaTypes(): MediaTypes
    {
        $this->ensureLoggedIn();

        return new MediaTypes($this->client);
    }

    /**
     * Get MFAs resource
     */
    public function mfas(): MFAs
    {
        $this->ensureLoggedIn();

        return new MFAs($this->client);
    }

    /**
     * Get modules resource
     */
    public function modules(): Modules
    {
        $this->ensureLoggedIn();

        return new Modules($this->client);
    }

    /**
     * Get proxies resource
     */
    public function proxies(): Proxies
    {
        $this->ensureLoggedIn();

        return new Proxies($this->client);
    }

    /**
     * Get proxy groups resource
     */
    public function proxyGroups(): ProxyGroups
    {
        $this->ensureLoggedIn();

        return new ProxyGroups($this->client);
    }

    /**
     * Get regular expressions resource
     */
    public function regularExpressions(): RegularExpressions
    {
        $this->ensureLoggedIn();

        return new RegularExpressions($this->client);
    }

    /**
     * Get reports resource
     */
    public function reports(): Reports
    {
        $this->ensureLoggedIn();

        return new Reports($this->client);
    }

    /**
     * Get roles resource
     */
    public function roles(): Roles
    {
        $this->ensureLoggedIn();

        return new Roles($this->client);
    }

    /**
     * Get scripts resource
     */
    public function scripts(): Scripts
    {
        $this->ensureLoggedIn();

        return new Scripts($this->client);
    }

    /**
     * Get services resource
     */
    public function services(): Services
    {
        $this->ensureLoggedIn();

        return new Services($this->client);
    }

    /**
     * Get settings resource
     */
    public function settings(): Settings
    {
        $this->ensureLoggedIn();

        return new Settings($this->client);
    }

    /**
     * Get SLAs resource
     */
    public function slas(): SLAs
    {
        $this->ensureLoggedIn();

        return new SLAs($this->client);
    }

    /**
     * Get tasks resource
     */
    public function tasks(): Tasks
    {
        $this->ensureLoggedIn();

        return new Tasks($this->client);
    }

    /**
     * Get template dashboards resource
     */
    public function templateDashboards(): TemplateDashboards
    {
        $this->ensureLoggedIn();

        return new TemplateDashboards($this->client);
    }

    /**
     * Get template groups resource
     */
    public function templateGroups(): TemplateGroups
    {
        $this->ensureLoggedIn();

        return new TemplateGroups($this->client);
    }

    /**
     * Get trends resource
     */
    public function trends(): Trends
    {
        $this->ensureLoggedIn();

        return new Trends($this->client);
    }

    /**
     * Get trigger prototypes resource
     */
    public function triggerPrototypes(): TriggerPrototypes
    {
        $this->ensureLoggedIn();

        return new TriggerPrototypes($this->client);
    }

    /**
     * Get user directories resource
     */
    public function userDirectories(): UserDirectories
    {
        $this->ensureLoggedIn();

        return new UserDirectories($this->client);
    }

    /**
     * Get user macros resource
     */
    public function userMacros(): UserMacros
    {
        $this->ensureLoggedIn();

        return new UserMacros($this->client);
    }

    /**
     * Get value maps resource
     */
    public function valueMaps(): ValueMaps
    {
        $this->ensureLoggedIn();

        return new ValueMaps($this->client);
    }

    /**
     * Get web scenarios resource
     */
    public function webScenarios(): WebScenarios
    {
        $this->ensureLoggedIn();

        return new WebScenarios($this->client);
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
