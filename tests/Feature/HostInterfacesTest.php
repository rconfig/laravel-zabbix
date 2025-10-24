<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists host interfaces', function () {
    ZabbixApi::login();
    $res = ZabbixApi::hostInterfaces()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
