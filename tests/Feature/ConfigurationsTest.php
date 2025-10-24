<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('gets configuration', function () {
    ZabbixApi::login();
    $res = ZabbixApi::configurations()->get([]);
    expect($res)->toBeArray();
});
