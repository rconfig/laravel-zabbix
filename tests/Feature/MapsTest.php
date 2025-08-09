<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists maps', function () {
    $res = Zabbix::maps()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
