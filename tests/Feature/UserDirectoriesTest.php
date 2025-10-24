<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists user directories', function () {
    ZabbixApi::login();
    $res = ZabbixApi::userDirectories()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
