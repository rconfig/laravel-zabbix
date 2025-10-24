<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists template groups', function () {
    ZabbixApi::login();
    $res = ZabbixApi::templateGroups()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
