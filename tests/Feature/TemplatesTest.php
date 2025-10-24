<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists templates', function () {
    ZabbixApi::login();
    $tpl = ZabbixApi::templates()->get(['output' => ['templateid', 'host']]);
    expect($tpl)->toBeArray();
});
