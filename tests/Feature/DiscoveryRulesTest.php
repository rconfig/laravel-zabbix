<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists discovery rules', function () {
    ZabbixApi::login();
    $res = ZabbixApi::discoveryRules()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
