<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists dashboards', function () {
    $res = Zabbix::dashboards()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
