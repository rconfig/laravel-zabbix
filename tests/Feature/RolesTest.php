<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists roles', function () {
    $res = Zabbix::roles()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
