<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists maintenances', function () {
    ZabbixApi::login();
    $m = ZabbixApi::maintenances()->get(['output' => 'extend', 'limit' => 5]);
    expect($m)->toBeArray();
});
