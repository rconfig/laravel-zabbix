<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists trigger prototypes', function () {
    ZabbixApi::login();
    $res = ZabbixApi::triggerPrototypes()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
