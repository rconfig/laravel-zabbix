<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists maintenances', function () {
    $m = Zabbix::maintenances()->get(['output' => 'extend', 'limit' => 5]);
    expect($m)->toBeArray();
});
