<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists graph prototypes', function () {
    ZabbixApi::login();
    $res = ZabbixApi::graphPrototypes()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
