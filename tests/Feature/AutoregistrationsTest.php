<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists autoregistrations', function () {
    ZabbixApi::login();
    $res = ZabbixApi::autoregistrations()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
