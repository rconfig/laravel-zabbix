<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists proxy groups', function () {
    ZabbixApi::login();
    $res = ZabbixApi::proxyGroups()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
