<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists discovery checks', function () {
    ZabbixApi::login();
    $res = ZabbixApi::discoveryChecks()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
