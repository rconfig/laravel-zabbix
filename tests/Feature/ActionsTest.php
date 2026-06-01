<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists actions', function () {
    ZabbixApi::login();
    $res = ZabbixApi::actions()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
