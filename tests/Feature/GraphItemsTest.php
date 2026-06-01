<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists graph items', function () {
    ZabbixApi::login();
    $res = ZabbixApi::graphItems()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
