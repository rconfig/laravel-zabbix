<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists icon maps', function () {
    $res = Zabbix::iconMaps()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
