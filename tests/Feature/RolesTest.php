<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists roles', function () {
    ZabbixApi::login();
    $res = ZabbixApi::roles()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
