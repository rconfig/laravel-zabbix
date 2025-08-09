<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists discovered hosts', function () {
    $res = Zabbix::discoveredHosts()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
