<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists users', function () {
    ZabbixApi::login();
    $u = ZabbixApi::users()->get(['output' => ['userid', 'username']]);
    expect($u)->toBeArray();
});
