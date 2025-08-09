<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('gets MFA settings', function () {
    $res = Zabbix::mfas()->get([]);
    expect($res)->toBeArray();
});
