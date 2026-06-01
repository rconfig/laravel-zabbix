<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists value maps', function () {
    ZabbixApi::login();
    $res = ZabbixApi::valueMaps()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
