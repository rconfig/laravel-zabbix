<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists tasks', function () {
    ZabbixApi::login();
    $res = ZabbixApi::tasks()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
