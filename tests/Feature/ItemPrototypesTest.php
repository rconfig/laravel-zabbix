<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists item prototypes', function () {
    $res = Zabbix::itemPrototypes()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
