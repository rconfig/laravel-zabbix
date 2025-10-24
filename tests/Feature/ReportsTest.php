<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists reports', function () {
    ZabbixApi::login();
    $res = ZabbixApi::reports()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
