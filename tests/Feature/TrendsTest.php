<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('gets trends', function () {
    ZabbixApi::login();
    $res = ZabbixApi::trends()->get(['itemids' => ['30001'], 'limit' => 1]);
    expect($res)->toBeArray();
});
