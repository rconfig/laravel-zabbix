<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists HA nodes', function () {
    ZabbixApi::login();
    $res = ZabbixApi::highAvailabilityNodes()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
