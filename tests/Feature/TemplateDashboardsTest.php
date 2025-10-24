<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists template dashboards', function () {
    ZabbixApi::login();
    $res = ZabbixApi::templateDashboards()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
