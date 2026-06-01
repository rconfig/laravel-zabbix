<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists scripts', function () {
    ZabbixApi::login();
    $res = ZabbixApi::scripts()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
