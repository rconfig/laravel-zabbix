<?php

namespace Rconfig\Zabbix\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Rconfig\Zabbix\Resources\Hosts hosts()
 * @method static \Rconfig\Zabbix\Resources\HostGroups hostGroups()
 * @method static array apiVersion()
 */
class Zabbix extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Rconfig\Zabbix\ZabbixManager::class;
    }
}
