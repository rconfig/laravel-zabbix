<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists host interfaces', function () {
    $res = Zabbix::hostInterfaces()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
