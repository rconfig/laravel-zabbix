<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists discovered services', function () {
    ZabbixApi::login();
    $res = ZabbixApi::discoveredServices()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
