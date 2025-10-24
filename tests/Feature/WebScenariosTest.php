<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists web scenarios', function () {
    ZabbixApi::login();
    $res = ZabbixApi::webScenarios()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
