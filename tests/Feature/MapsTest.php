<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists maps', function () {
    ZabbixApi::login();
    $res = ZabbixApi::maps()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
