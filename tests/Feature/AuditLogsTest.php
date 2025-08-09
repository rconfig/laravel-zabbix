<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists audit logs', function () {
    $res = Zabbix::auditLogs()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
