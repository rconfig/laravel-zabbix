<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists events', function () {
    ZabbixApi::login();
    $res = ZabbixApi::events()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
