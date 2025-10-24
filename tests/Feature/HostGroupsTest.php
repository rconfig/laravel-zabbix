<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists host groups', function () {
    ZabbixApi::login();
    $groups = ZabbixApi::hostGroups()->all(5);
    expect($groups)->toBeArray();
});
