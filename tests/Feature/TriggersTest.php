<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists triggers', function () {
    ZabbixApi::login();
    $triggers = ZabbixApi::triggers()->get(['output' => ['triggerid', 'description'], 'limit' => 5]);
    expect($triggers)->toBeArray();
});
