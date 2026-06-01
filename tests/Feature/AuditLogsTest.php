<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists audit logs', function () {
    ZabbixApi::login();
    $res = ZabbixApi::auditLogs()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
