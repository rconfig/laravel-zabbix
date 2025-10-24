<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists icon maps', function () {
    ZabbixApi::login();
    $res = ZabbixApi::iconMaps()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
