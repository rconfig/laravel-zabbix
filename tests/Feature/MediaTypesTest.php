<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists media types', function () {
    $res = Zabbix::mediaTypes()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
