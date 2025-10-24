<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists graphs', function () {
    ZabbixApi::login();
    $res = ZabbixApi::graphs()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
