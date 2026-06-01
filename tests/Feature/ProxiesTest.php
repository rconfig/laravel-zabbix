<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists proxies', function () {
    ZabbixApi::login();
    $res = ZabbixApi::proxies()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
