<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists user directories', function () {
    $res = Zabbix::userDirectories()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
