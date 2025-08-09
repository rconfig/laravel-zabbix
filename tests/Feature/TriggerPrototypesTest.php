<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists trigger prototypes', function () {
    $res = Zabbix::triggerPrototypes()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
