<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists events', function () {
    $res = Zabbix::events()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
