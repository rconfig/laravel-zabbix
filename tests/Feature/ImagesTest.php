<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists images', function () {
    ZabbixApi::login();
    $res = ZabbixApi::images()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
