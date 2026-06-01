<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('gets MFA settings', function () {
    ZabbixApi::login();
    $res = ZabbixApi::mfas()->get([]);
    expect($res)->toBeArray();
});
