<?php

namespace Rconfig\Zabbix;

use Rconfig\Zabbix\Contracts\ZabbixClient;

class ZabbixManager
{
    public function __construct(protected ZabbixClient $client) {}

    // --- Core ---
    public function apiVersion(): string
    {
        /** @var string $ver */
        $ver = $this->client->call('apiinfo.version', []);

        return $ver;
    }

    // --- Existing (done) ---
    public function hosts(): \Rconfig\Zabbix\Resources\Hosts
    {
        return new \Rconfig\Zabbix\Resources\Hosts($this->client);
    }

    public function hostGroups(): \Rconfig\Zabbix\Resources\HostGroups
    {
        return new \Rconfig\Zabbix\Resources\HostGroups($this->client);
    }

    public function items(): \Rconfig\Zabbix\Resources\Items
    {
        return new \Rconfig\Zabbix\Resources\Items($this->client);
    }

    public function maintenances(): \Rconfig\Zabbix\Resources\Maintenances
    {
        return new \Rconfig\Zabbix\Resources\Maintenances($this->client);
    }

    public function problems(): \Rconfig\Zabbix\Resources\Problems
    {
        return new \Rconfig\Zabbix\Resources\Problems($this->client);
    }

    public function templates(): \Rconfig\Zabbix\Resources\Templates
    {
        return new \Rconfig\Zabbix\Resources\Templates($this->client);
    }

    public function tokens(): \Rconfig\Zabbix\Resources\Tokens
    {
        return new \Rconfig\Zabbix\Resources\Tokens($this->client);
    }

    public function triggers(): \Rconfig\Zabbix\Resources\Triggers
    {
        return new \Rconfig\Zabbix\Resources\Triggers($this->client);
    }

    public function users(): \Rconfig\Zabbix\Resources\Users
    {
        return new \Rconfig\Zabbix\Resources\Users($this->client);
    }

    public function userGroups(): \Rconfig\Zabbix\Resources\UserGroups
    {
        return new \Rconfig\Zabbix\Resources\UserGroups($this->client);
    }

    // --- New resources accessors ---
    public function actions(): \Rconfig\Zabbix\Resources\Actions
    {
        return new \Rconfig\Zabbix\Resources\Actions($this->client);
    }

    public function alerts(): \Rconfig\Zabbix\Resources\Alerts
    {
        return new \Rconfig\Zabbix\Resources\Alerts($this->client);
    }

    public function auditLogs(): \Rconfig\Zabbix\Resources\AuditLogs
    {
        return new \Rconfig\Zabbix\Resources\AuditLogs($this->client);
    }

    public function authentications(): \Rconfig\Zabbix\Resources\Authentications
    {
        return new \Rconfig\Zabbix\Resources\Authentications($this->client);
    }

    public function authenticationObjects(): \Rconfig\Zabbix\Resources\AuthenticationObjects
    {
        return new \Rconfig\Zabbix\Resources\AuthenticationObjects($this->client);
    }

    public function autoregistrations(): \Rconfig\Zabbix\Resources\Autoregistrations
    {
        return new \Rconfig\Zabbix\Resources\Autoregistrations($this->client);
    }

    public function configurations(): \Rconfig\Zabbix\Resources\Configurations
    {
        return new \Rconfig\Zabbix\Resources\Configurations($this->client);
    }

    public function connectors(): \Rconfig\Zabbix\Resources\Connectors
    {
        return new \Rconfig\Zabbix\Resources\Connectors($this->client);
    }

    public function correlations(): \Rconfig\Zabbix\Resources\Correlations
    {
        return new \Rconfig\Zabbix\Resources\Correlations($this->client);
    }

    public function dashboards(): \Rconfig\Zabbix\Resources\Dashboards
    {
        return new \Rconfig\Zabbix\Resources\Dashboards($this->client);
    }

    public function discoveredHosts(): \Rconfig\Zabbix\Resources\DiscoveredHosts
    {
        return new \Rconfig\Zabbix\Resources\DiscoveredHosts($this->client);
    }

    public function discoveredServices(): \Rconfig\Zabbix\Resources\DiscoveredServices
    {
        return new \Rconfig\Zabbix\Resources\DiscoveredServices($this->client);
    }

    public function discoveryChecks(): \Rconfig\Zabbix\Resources\DiscoveryChecks
    {
        return new \Rconfig\Zabbix\Resources\DiscoveryChecks($this->client);
    }

    public function discoveryRules(): \Rconfig\Zabbix\Resources\DiscoveryRules
    {
        return new \Rconfig\Zabbix\Resources\DiscoveryRules($this->client);
    }

    public function events(): \Rconfig\Zabbix\Resources\Events
    {
        return new \Rconfig\Zabbix\Resources\Events($this->client);
    }

    public function graphs(): \Rconfig\Zabbix\Resources\Graphs
    {
        return new \Rconfig\Zabbix\Resources\Graphs($this->client);
    }

    public function graphItems(): \Rconfig\Zabbix\Resources\GraphItems
    {
        return new \Rconfig\Zabbix\Resources\GraphItems($this->client);
    }

    public function graphPrototypes(): \Rconfig\Zabbix\Resources\GraphPrototypes
    {
        return new \Rconfig\Zabbix\Resources\GraphPrototypes($this->client);
    }

    public function highAvailabilityNodes(): \Rconfig\Zabbix\Resources\HighAvailabilityNodes
    {
        return new \Rconfig\Zabbix\Resources\HighAvailabilityNodes($this->client);
    }

    public function histories(): \Rconfig\Zabbix\Resources\Histories
    {
        return new \Rconfig\Zabbix\Resources\Histories($this->client);
    }

    public function hostInterfaces(): \Rconfig\Zabbix\Resources\HostInterfaces
    {
        return new \Rconfig\Zabbix\Resources\HostInterfaces($this->client);
    }

    public function hostPrototypes(): \Rconfig\Zabbix\Resources\HostPrototypes
    {
        return new \Rconfig\Zabbix\Resources\HostPrototypes($this->client);
    }

    public function housekeeping(): \Rconfig\Zabbix\Resources\Housekeeping
    {
        return new \Rconfig\Zabbix\Resources\Housekeeping($this->client);
    }

    public function iconMaps(): \Rconfig\Zabbix\Resources\IconMaps
    {
        return new \Rconfig\Zabbix\Resources\IconMaps($this->client);
    }

    public function images(): \Rconfig\Zabbix\Resources\Images
    {
        return new \Rconfig\Zabbix\Resources\Images($this->client);
    }

    public function itemPrototypes(): \Rconfig\Zabbix\Resources\ItemPrototypes
    {
        return new \Rconfig\Zabbix\Resources\ItemPrototypes($this->client);
    }

    public function lldRules(): \Rconfig\Zabbix\Resources\LLDRules
    {
        return new \Rconfig\Zabbix\Resources\LLDRules($this->client);
    }

    public function maps(): \Rconfig\Zabbix\Resources\Maps
    {
        return new \Rconfig\Zabbix\Resources\Maps($this->client);
    }

    public function mediaTypes(): \Rconfig\Zabbix\Resources\MediaTypes
    {
        return new \Rconfig\Zabbix\Resources\MediaTypes($this->client);
    }

    public function mfas(): \Rconfig\Zabbix\Resources\MFAs
    {
        return new \Rconfig\Zabbix\Resources\MFAs($this->client);
    }

    public function modules(): \Rconfig\Zabbix\Resources\Modules
    {
        return new \Rconfig\Zabbix\Resources\Modules($this->client);
    }

    public function proxies(): \Rconfig\Zabbix\Resources\Proxies
    {
        return new \Rconfig\Zabbix\Resources\Proxies($this->client);
    }

    public function proxyGroups(): \Rconfig\Zabbix\Resources\ProxyGroups
    {
        return new \Rconfig\Zabbix\Resources\ProxyGroups($this->client);
    }

    public function regularExpressions(): \Rconfig\Zabbix\Resources\RegularExpressions
    {
        return new \Rconfig\Zabbix\Resources\RegularExpressions($this->client);
    }

    public function reports(): \Rconfig\Zabbix\Resources\Reports
    {
        return new \Rconfig\Zabbix\Resources\Reports($this->client);
    }

    public function roles(): \Rconfig\Zabbix\Resources\Roles
    {
        return new \Rconfig\Zabbix\Resources\Roles($this->client);
    }

    public function scripts(): \Rconfig\Zabbix\Resources\Scripts
    {
        return new \Rconfig\Zabbix\Resources\Scripts($this->client);
    }

    public function services(): \Rconfig\Zabbix\Resources\Services
    {
        return new \Rconfig\Zabbix\Resources\Services($this->client);
    }

    public function settings(): \Rconfig\Zabbix\Resources\Settings
    {
        return new \Rconfig\Zabbix\Resources\Settings($this->client);
    }

    public function slas(): \Rconfig\Zabbix\Resources\SLAs
    {
        return new \Rconfig\Zabbix\Resources\SLAs($this->client);
    }

    public function tasks(): \Rconfig\Zabbix\Resources\Tasks
    {
        return new \Rconfig\Zabbix\Resources\Tasks($this->client);
    }

    public function templateDashboards(): \Rconfig\Zabbix\Resources\TemplateDashboards
    {
        return new \Rconfig\Zabbix\Resources\TemplateDashboards($this->client);
    }

    public function templateGroups(): \Rconfig\Zabbix\Resources\TemplateGroups
    {
        return new \Rconfig\Zabbix\Resources\TemplateGroups($this->client);
    }

    public function trends(): \Rconfig\Zabbix\Resources\Trends
    {
        return new \Rconfig\Zabbix\Resources\Trends($this->client);
    }

    public function triggerPrototypes(): \Rconfig\Zabbix\Resources\TriggerPrototypes
    {
        return new \Rconfig\Zabbix\Resources\TriggerPrototypes($this->client);
    }

    public function userDirectories(): \Rconfig\Zabbix\Resources\UserDirectories
    {
        return new \Rconfig\Zabbix\Resources\UserDirectories($this->client);
    }

    public function userMacros(): \Rconfig\Zabbix\Resources\UserMacros
    {
        return new \Rconfig\Zabbix\Resources\UserMacros($this->client);
    }

    public function valueMaps(): \Rconfig\Zabbix\Resources\ValueMaps
    {
        return new \Rconfig\Zabbix\Resources\ValueMaps($this->client);
    }

    public function webScenarios(): \Rconfig\Zabbix\Resources\WebScenarios
    {
        return new \Rconfig\Zabbix\Resources\WebScenarios($this->client);
    }
}
