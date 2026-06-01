<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists dashboards', function () {
    ZabbixApi::login();
    $res = ZabbixApi::dashboards()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
