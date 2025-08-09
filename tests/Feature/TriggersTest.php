<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists triggers', function () {
    $triggers = Zabbix::triggers()->get(['output' => ['triggerid', 'description'], 'limit' => 5]);
    expect($triggers)->toBeArray();
});
