<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists modules', function () {
    $res = Zabbix::modules()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
