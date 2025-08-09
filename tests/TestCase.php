<?php

namespace Rconfig\Zabbix\Tests;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as Orchestra;
use Rconfig\Zabbix\ZabbixServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [ZabbixServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa=');
        $app['config']->set('zabbix.fake_by_default', true);
        $app['config']->set('zabbix.base_url', 'http://localhost');
        $app['config']->set('zabbix.endpoint', '/api_jsonrpc.php');
        $app['config']->set('zabbix.timeout', 5);
        $app['config']->set('zabbix.retries', 0);
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (! env('RUN_ZABBIX_INTEGRATION')) {
            Http::preventStrayRequests();

            Http::fake(function ($request) {
                $payload = json_decode($request->body(), true) ?? [];
                $method = $payload['method'] ?? '';

                // Helper to format a standard JSON-RPC result
                $ok = function ($result) {
                    return Http::response(['jsonrpc' => '2.0', 'result' => $result, 'id' => 1], 200);
                };

                // --- Core: apiinfo.version (string result, no auth) ---
                if ($method === 'apiinfo.version') {
                    return Http::response(['jsonrpc' => '2.0', 'result' => '7.0.0', 'id' => 1], 200);
                }

                // --- Core Resources ---
                if ($method === 'host.get') {
                    return $ok([[
                        'hostid' => '10106',
                        'host' => 'web-01',
                        'status' => 0,
                        'interfaces' => [['ip' => '10.0.0.11', 'type' => 1]],
                        'groups' => [['groupid' => '2', 'name' => 'Linux servers']],
                    ]]);
                }

                if ($method === 'hostgroup.get') {
                    return $ok([['groupid' => '2', 'name' => 'Linux servers']]);
                }

                if ($method === 'item.get') {
                    return $ok([['itemid' => '30001', 'name' => 'CPU load', 'hostid' => '10106']]);
                }

                if ($method === 'trigger.get') {
                    return $ok([['triggerid' => '40001', 'description' => 'High CPU load']]);
                }

                if ($method === 'problem.get') {
                    return $ok([['eventid' => '50001', 'name' => 'CPU over threshold']]);
                }

                if ($method === 'maintenance.get') {
                    return $ok([['maintenanceid' => '60001', 'name' => 'Nightly window']]);
                }

                if ($method === 'template.get') {
                    return $ok([['templateid' => '10001', 'host' => 'Template OS Linux']]);
                }

                if ($method === 'user.get') {
                    return $ok([['userid' => '1', 'username' => 'Admin']]);
                }

                if ($method === 'usergroup.get') {
                    return $ok([['usrgrpid' => '7', 'name' => 'Zabbix administrators']]);
                }

                if ($method === 'token.get') {
                    return $ok([['tokenid' => '90001', 'name' => 'CI Token']]);
                }

                // --- Administrative and Configuration Resources ---
                if ($method === 'action.get') {
                    return $ok([['actionid' => '1', 'name' => 'Notify on problem']]);
                }

                if ($method === 'alert.get') {
                    return $ok([['alertid' => '10', 'subject' => 'Problem: CPU high']]);
                }

                if ($method === 'auditlog.get') {
                    return $ok([['auditid' => '100', 'action' => 'user.login']]);
                }

                if ($method === 'authentication.get') {
                    return $ok([['authmode' => 'internal']]);
                }

                if ($method === 'authenticationobject.get') {
                    return $ok([['objectid' => '1', 'name' => 'LDAP']]);
                }

                if ($method === 'autoregistration.get') {
                    return $ok([['autoregistrationid' => '1', 'name' => 'Auto add hosts']]);
                }

                if ($method === 'configuration.get') {
                    return $ok([['configid' => '1', 'hk_events_mode' => 1]]);
                }

                if ($method === 'connector.get') {
                    return $ok([['connectorid' => '1', 'name' => 'Webhook A']]);
                }

                if ($method === 'correlation.get') {
                    return $ok([['correlationid' => '1', 'name' => 'Deduplicate CPU alerts']]);
                }

                if ($method === 'role.get') {
                    return $ok([['roleid' => '1', 'name' => 'Read only']]);
                }

                if ($method === 'script.get') {
                    return $ok([['scriptid' => '1', 'name' => 'Ping host']]);
                }

                if ($method === 'settings.get') {
                    return $ok([['key' => 'ui-theme', 'value' => 'dark']]);
                }

                if ($method === 'userdirectory.get') {
                    return $ok([['directoryid' => '1', 'type' => 'ldap']]);
                }

                if ($method === 'usermacro.get') {
                    return $ok([['macro' => '{$ENV}', 'value' => 'prod']]);
                }

                // --- Discovery Resources ---
                if ($method === 'dhost.get' || $method === 'discoveredhost.get') {
                    return $ok([['dhostid' => '1', 'status' => 0]]);
                }

                if ($method === 'dservice.get' || $method === 'discoveredservice.get') {
                    return $ok([['dserviceid' => '1', 'status' => 0]]);
                }

                if ($method === 'dcheck.get' || $method === 'discoverycheck.get') {
                    return $ok([['dcheckid' => '1', 'name' => 'ICMP ping']]);
                }

                if ($method === 'drule.get' || $method === 'discoveryrule.get') {
                    return $ok([['druleid' => '1', 'name' => 'Network scan']]);
                }

                // --- Monitoring and Data Resources ---
                if ($method === 'event.get') {
                    return $ok([['eventid' => '70001', 'name' => 'Trigger fired']]);
                }

                if ($method === 'graph.get') {
                    return $ok([['graphid' => '1', 'name' => 'CPU usage']]);
                }

                if ($method === 'graphitem.get') {
                    return $ok([['gitemid' => '1', 'itemid' => '30001']]);
                }

                if ($method === 'graphprototype.get') {
                    return $ok([['graphid' => '2', 'name' => 'CPU per core']]);
                }

                if ($method === 'history.get') {
                    return $ok([['itemid' => '30001', 'value' => '0.50', 'clock' => time()]]);
                }

                if ($method === 'trend.get') {
                    return $ok([['itemid' => '30001', 'num' => 100, 'clock' => time()]]);
                }

                // --- Template and Prototype Resources ---
                if ($method === 'hostprototype.get') {
                    return $ok([['hostid' => '20001', 'name' => 'AutoHost-{#SN}']]);
                }

                if ($method === 'itemprototype.get') {
                    return $ok([['itemid' => '31001', 'name' => 'CPU core {#CORE}']]);
                }

                if ($method === 'lldrule.get') {
                    return $ok([['itemid' => '32001', 'name' => 'LLD CPU cores']]);
                }

                if ($method === 'triggerprototype.get') {
                    return $ok([['triggerid' => '41001', 'description' => 'CPU core high']]);
                }

                if ($method === 'templatedashboard.get') {
                    return $ok([['dashboardid' => '2', 'name' => 'Template OS Linux']]);
                }

                if ($method === 'templategroup.get') {
                    return $ok([['groupid' => '10', 'name' => 'Linux templates']]);
                }

                // --- Interface and Network Resources ---
                if ($method === 'hostinterface.get') {
                    return $ok([['interfaceid' => '1', 'ip' => '10.0.0.11', 'type' => 1]]);
                }

                if ($method === 'proxy.get') {
                    return $ok([['proxyid' => '1', 'host' => 'zbx-proxy-01']]);
                }

                if ($method === 'proxygroup.get') {
                    return $ok([['proxy_groupid' => '1', 'name' => 'EU Proxies']]);
                }

                // --- UI and Presentation Resources ---
                if ($method === 'dashboard.get') {
                    return $ok([['dashboardid' => '1', 'name' => 'Operations']]);
                }

                if ($method === 'iconmap.get') {
                    return $ok([['iconmapid' => '1', 'name' => 'OS icons']]);
                }

                if ($method === 'image.get') {
                    return $ok([['imageid' => '1', 'name' => 'Logo']]);
                }

                if ($method === 'map.get') {
                    return $ok([['sysmapid' => '1', 'name' => 'Datacenter map']]);
                }

                if ($method === 'mediatype.get') {
                    return $ok([['mediatypeid' => '1', 'name' => 'Email']]);
                }

                if ($method === 'valuemap.get') {
                    return $ok([['valuemapid' => '1', 'name' => 'On/Off']]);
                }

                // --- Service and SLA Resources ---
                if ($method === 'service.get') {
                    return $ok([['serviceid' => '1', 'name' => 'Web app']]);
                }

                if ($method === 'sla.get') {
                    return $ok([['slaid' => '1', 'name' => 'Gold SLA']]);
                }

                if ($method === 'report.get') {
                    return $ok([['reportid' => '1', 'name' => 'Weekly SLA']]);
                }

                // --- Web Monitoring Resources ---
                if ($method === 'httptest.get' || $method === 'webscenario.get' || $method === 'webscenario.list') {
                    return $ok([['httptestid' => '1', 'name' => 'Homepage']]);
                }

                // --- System and Maintenance Resources ---
                if ($method === 'hanode.get') {
                    return $ok([['nodeid' => '1', 'name' => 'HA node 1', 'status' => 0]]);
                }

                if ($method === 'housekeeping.get') {
                    return $ok([['hk_events_mode' => 1]]);
                }

                if ($method === 'mfa.get') {
                    return $ok([['enabled' => 0]]);
                }

                if ($method === 'module.get') {
                    return $ok([['moduleid' => '1', 'name' => 'MyModule']]);
                }

                if ($method === 'task.get') {
                    return $ok([['taskid' => '1', 'status' => 0]]);
                }

                // --- Pattern Matching Resources (multiple aliases) ---
                if ($method === 'regex.get' || $method === 'regexp.get' || $method === 'regularexpression.get') {
                    return $ok([['regexid' => '1', 'name' => 'Host filter']]);
                }

                // Default empty response for unhandled methods
                return $ok([]);
            });
        }
    }

    /**
     * Get mock data for a given Zabbix API method
     */
    private function getMockDataForMethod(string $method): array
    {
        $mockDataMap = [
            // Core resources with detailed mock data
            'host.get' => [[
                'hostid' => '10106',
                'host' => 'web-01',
                'status' => 0,
                'interfaces' => [['ip' => '10.0.0.11', 'type' => 1]],
                'groups' => [['groupid' => '2', 'name' => 'Linux servers']],
            ]],
            'hostgroup.get' => [['groupid' => '2', 'name' => 'Linux servers']],
            'item.get' => [['itemid' => '30001', 'name' => 'CPU load', 'hostid' => '10106']],
            'trigger.get' => [['triggerid' => '40001', 'description' => 'High CPU load']],
            'problem.get' => [['eventid' => '50001', 'name' => 'CPU over threshold']],
            'maintenance.get' => [['maintenanceid' => '60001', 'name' => 'Nightly window']],
            'template.get' => [['templateid' => '10001', 'host' => 'Template OS Linux']],
            'user.get' => [['userid' => '1', 'username' => 'Admin']],
            'usergroup.get' => [['usrgrpid' => '7', 'name' => 'Zabbix administrators']],
            'token.get' => [['tokenid' => '90001', 'name' => 'CI Token']],

            // Administrative and configuration resources
            'action.get' => [['actionid' => '1', 'name' => 'Notify on problem']],
            'alert.get' => [['alertid' => '10', 'subject' => 'Problem: CPU high']],
            'auditlog.get' => [['auditid' => '100', 'action' => 'user.login']],
            'authentication.get' => [['authmode' => 'internal']],
            'authenticationobject.get' => [['objectid' => '1', 'name' => 'LDAP']],
            'autoregistration.get' => [['autoregistrationid' => '1', 'name' => 'Auto add hosts']],
            'configuration.get' => [['configid' => '1', 'hk_events_mode' => 1]],
            'connector.get' => [['connectorid' => '1', 'name' => 'Webhook A']],
            'correlation.get' => [['correlationid' => '1', 'name' => 'Deduplicate CPU alerts']],
            'role.get' => [['roleid' => '1', 'name' => 'Read only']],
            'script.get' => [['scriptid' => '1', 'name' => 'Ping host']],
            'settings.get' => [['key' => 'ui-theme', 'value' => 'dark']],
            'userdirectory.get' => [['directoryid' => '1', 'type' => 'ldap']],
            'usermacro.get' => [['macro' => '{$ENV}', 'value' => 'prod']],

            // Discovery resources
            'dhost.get' => [['dhostid' => '1', 'status' => 0]],
            'discoveredhost.get' => [['dhostid' => '1', 'status' => 0]],
            'dservice.get' => [['dserviceid' => '1', 'status' => 0]],
            'discoveredservice.get' => [['dserviceid' => '1', 'status' => 0]],
            'dcheck.get' => [['dcheckid' => '1', 'name' => 'ICMP ping']],
            'discoverycheck.get' => [['dcheckid' => '1', 'name' => 'ICMP ping']],
            'drule.get' => [['druleid' => '1', 'name' => 'Network scan']],
            'discoveryrule.get' => [['druleid' => '1', 'name' => 'Network scan']],

            // Monitoring and data resources
            'event.get' => [['eventid' => '70001', 'name' => 'Trigger fired']],
            'graph.get' => [['graphid' => '1', 'name' => 'CPU usage']],
            'graphitem.get' => [['gitemid' => '1', 'itemid' => '30001']],
            'graphprototype.get' => [['graphid' => '2', 'name' => 'CPU per core']],
            'history.get' => [['itemid' => '30001', 'value' => '0.50', 'clock' => time()]],
            'trend.get' => [['itemid' => '30001', 'num' => 100, 'clock' => time()]],

            // Template and prototype resources
            'hostprototype.get' => [['hostid' => '20001', 'name' => 'AutoHost-{#SN}']],
            'itemprototype.get' => [['itemid' => '31001', 'name' => 'CPU core {#CORE}']],
            'lldrule.get' => [['itemid' => '32001', 'name' => 'LLD CPU cores']],
            'triggerprototype.get' => [['triggerid' => '41001', 'description' => 'CPU core high']],
            'templatedashboard.get' => [['dashboardid' => '2', 'name' => 'Template OS Linux']],
            'templategroup.get' => [['groupid' => '10', 'name' => 'Linux templates']],

            // Interface and network resources
            'hostinterface.get' => [['interfaceid' => '1', 'ip' => '10.0.0.11', 'type' => 1]],
            'proxy.get' => [['proxyid' => '1', 'host' => 'zbx-proxy-01']],
            'proxygroup.get' => [['proxy_groupid' => '1', 'name' => 'EU Proxies']],

            // UI and presentation resources
            'dashboard.get' => [['dashboardid' => '1', 'name' => 'Operations']],
            'iconmap.get' => [['iconmapid' => '1', 'name' => 'OS icons']],
            'image.get' => [['imageid' => '1', 'name' => 'Logo']],
            'map.get' => [['sysmapid' => '1', 'name' => 'Datacenter map']],
            'mediatype.get' => [['mediatypeid' => '1', 'name' => 'Email']],
            'valuemap.get' => [['valuemapid' => '1', 'name' => 'On/Off']],

            // Service and SLA resources
            'service.get' => [['serviceid' => '1', 'name' => 'Web app']],
            'sla.get' => [['slaid' => '1', 'name' => 'Gold SLA']],
            'report.get' => [['reportid' => '1', 'name' => 'Weekly SLA']],

            // Web monitoring resources
            'httptest.get' => [['httptestid' => '1', 'name' => 'Homepage']],
            'webscenario.get' => [['httptestid' => '1', 'name' => 'Homepage']],
            'webscenario.list' => [['httptestid' => '1', 'name' => 'Homepage']],

            // System and maintenance resources
            'hanode.get' => [['nodeid' => '1', 'name' => 'HA node 1', 'status' => 0]],
            'housekeeping.get' => [['hk_events_mode' => 1]],
            'mfa.get' => [['enabled' => 0]],
            'module.get' => [['moduleid' => '1', 'name' => 'MyModule']],
            'task.get' => [['taskid' => '1', 'status' => 0]],

            // Pattern matching resources (multiple possible names)
            'regex.get' => [['regexid' => '1', 'name' => 'Host filter']],
            'regexp.get' => [['regexid' => '1', 'name' => 'Host filter']],
            'regularexpression.get' => [['regexid' => '1', 'name' => 'Host filter']],
        ];

        return $mockDataMap[$method] ?? [];
    }
}
