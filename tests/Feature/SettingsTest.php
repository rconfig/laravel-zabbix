<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('gets settings', function () {
    ZabbixApi::login();
    $res = ZabbixApi::settings()->get([]);
    expect($res)->toBeArray();
});
