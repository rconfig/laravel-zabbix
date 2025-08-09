<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists tasks', function () {
    $res = Zabbix::tasks()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
