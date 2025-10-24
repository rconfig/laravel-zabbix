<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists items', function () {
    ZabbixApi::login();
    $items = ZabbixApi::items()->get(['hostids' => ['10106'], 'output' => ['itemid', 'name']]);
    expect($items)->toBeArray()->not->toBeEmpty();
});
