<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('gets housekeeping', function () {
    ZabbixApi::login();
    $res = ZabbixApi::housekeeping()->get([]);
    expect($res)->toBeArray();
});
