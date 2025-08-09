<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists actions', function () {
    $res = Zabbix::actions()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
