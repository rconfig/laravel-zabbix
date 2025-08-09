<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists reports', function () {
    $res = Zabbix::reports()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
