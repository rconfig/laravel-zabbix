<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists host prototypes', function () {
    ZabbixApi::login();
    $res = ZabbixApi::hostPrototypes()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
