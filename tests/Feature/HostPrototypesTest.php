<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists host prototypes', function () {
    $res = Zabbix::hostPrototypes()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
