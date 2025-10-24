<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists problems', function () {
    ZabbixApi::login();
    $problems = ZabbixApi::problems()->get(['recent' => 'true', 'limit' => 5]);
    expect($problems)->toBeArray();
});
