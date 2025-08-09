<?php

namespace Rconfig\Zabbix\Contracts;

interface ZabbixClient
{
    /**
     * @param  string  $method  JSON-RPC method (e.g. host.get)
     * @param  array<string,mixed>  $params
     * @return mixed // string|array depending on the Zabbix method
     */
    public function call(string $method, array $params): mixed;
}
