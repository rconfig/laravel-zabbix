<?php

namespace Rconfig\Zabbix\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Rconfig\Zabbix\ZabbixConnector login(?string $baseUrl = null, ?string $username = null, ?string $password = null, array $options = [])
 * @method static \Rconfig\Zabbix\ZabbixConnector loginWithToken(string $baseUrl, string $token, array $options = [])
 * @method static bool isLoggedIn()
 * @method static string apiVersion()
 * @method static mixed call(string $method, array $params = [])
 *
 * // Configuration getters
 * @method static bool isDebugEnabled()
 * @method static int getTimeout()
 * @method static int getConnectTimeout()
 * @method static array getSslSettings()
 * @method static bool isGzipEnabled()
 *
 * // Resources
 * @method static \Rconfig\Zabbix\Resources\Actions actions()
 * @method static \Rconfig\Zabbix\Resources\Alerts alerts()
 * @method static \Rconfig\Zabbix\Resources\AuditLogs auditLogs()
 * @method static \Rconfig\Zabbix\Resources\Autoregistrations autoregistrations()
 * @method static \Rconfig\Zabbix\Resources\Configurations configurations()
 * @method static \Rconfig\Zabbix\Resources\Connectors connectors()
 * @method static \Rconfig\Zabbix\Resources\Correlations correlations()
 * @method static \Rconfig\Zabbix\Resources\Dashboards dashboards()
 * @method static \Rconfig\Zabbix\Resources\Events events()
 * @method static \Rconfig\Zabbix\Resources\Histories histories()
 * @method static \Rconfig\Zabbix\Resources\Hosts hosts()
 * @method static \Rconfig\Zabbix\Resources\HostGroups hostGroups()
 * @method static \Rconfig\Zabbix\Resources\Items items()
 * @method static \Rconfig\Zabbix\Resources\Maintenances maintenances()
 * @method static \Rconfig\Zabbix\Resources\Problems problems()
 * @method static \Rconfig\Zabbix\Resources\Templates templates()
 * @method static \Rconfig\Zabbix\Resources\Tokens tokens()
 * @method static \Rconfig\Zabbix\Resources\Triggers triggers()
 * @method static \Rconfig\Zabbix\Resources\Users users()
 * @method static \Rconfig\Zabbix\Resources\UserGroups userGroups()
 * @method static void logout()
 */
class ZabbixApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Rconfig\Zabbix\ZabbixConnector::class;
    }
}
