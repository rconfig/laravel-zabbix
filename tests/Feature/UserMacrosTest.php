<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists user macros', function () {
    ZabbixApi::login();
    $res = ZabbixApi::userMacros()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
