<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists correlations', function () {
    ZabbixApi::login();
    $res = ZabbixApi::correlations()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
