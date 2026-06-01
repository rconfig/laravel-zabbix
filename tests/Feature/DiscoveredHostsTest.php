<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists discovered hosts', function () {
    ZabbixApi::login();
    $res = ZabbixApi::discoveredHosts()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
