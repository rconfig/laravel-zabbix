<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists autoregistrations', function () {
    $res = Zabbix::autoregistrations()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
