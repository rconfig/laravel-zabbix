<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists scripts', function () {
    $res = Zabbix::scripts()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
