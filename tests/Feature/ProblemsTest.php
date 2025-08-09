<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists problems', function () {
    $problems = Zabbix::problems()->get(['recent' => 'true', 'limit' => 5]);
    expect($problems)->toBeArray();
});
