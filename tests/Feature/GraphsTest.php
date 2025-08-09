<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists graphs', function () {
    $res = Zabbix::graphs()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
