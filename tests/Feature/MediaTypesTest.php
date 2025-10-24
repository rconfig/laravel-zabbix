<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists media types', function () {
    ZabbixApi::login();
    $res = ZabbixApi::mediaTypes()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
