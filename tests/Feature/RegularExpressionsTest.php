<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists regular expressions', function () {
    ZabbixApi::login();
    $res = ZabbixApi::regularExpressions()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
