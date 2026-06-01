<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists item prototypes', function () {
    ZabbixApi::login();
    $res = ZabbixApi::itemPrototypes()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
