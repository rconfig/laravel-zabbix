<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('gets histories', function () {
    ZabbixApi::login();
    $res = ZabbixApi::histories()->get(['itemids' => ['30001'], 'history' => 0, 'limit' => 1]);
    expect($res)->toBeArray();
});
