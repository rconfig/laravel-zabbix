<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists SLAs', function () {
    ZabbixApi::login();
    $res = ZabbixApi::slas()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
