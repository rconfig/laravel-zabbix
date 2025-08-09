<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists services', function () {
    $res = Zabbix::services()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
