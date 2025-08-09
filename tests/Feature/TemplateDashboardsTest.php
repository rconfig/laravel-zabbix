<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists template dashboards', function () {
    $res = Zabbix::templateDashboards()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
