<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists alerts', function () {
    ZabbixApi::login();
    $res = ZabbixApi::alerts()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
