<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists connectors', function () {
    ZabbixApi::login();
    $res = ZabbixApi::connectors()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
