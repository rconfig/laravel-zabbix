<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists discovered services', function () {
    $res = Zabbix::discoveredServices()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
