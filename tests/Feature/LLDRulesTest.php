<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists LLD rules', function () {
    ZabbixApi::login();
    $res = ZabbixApi::lldRules()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
