<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('gets tokens (fake)', function () {
    ZabbixApi::login();
    $t = ZabbixApi::tokens()->get(['output' => 'extend', 'limit' => 2]);
    expect($t)->toBeArray();
});
