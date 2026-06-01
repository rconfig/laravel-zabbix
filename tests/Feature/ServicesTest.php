<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists services', function () {
    ZabbixApi::login();
    $res = ZabbixApi::services()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
