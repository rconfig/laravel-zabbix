<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists graph items', function () {
    $res = Zabbix::graphItems()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
