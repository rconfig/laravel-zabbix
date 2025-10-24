<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists modules', function () {
    ZabbixApi::login();
    $res = ZabbixApi::modules()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
