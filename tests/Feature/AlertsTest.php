<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists alerts', function () {
    $res = Zabbix::alerts()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
