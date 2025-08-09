<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('gets settings', function () {
    $res = Zabbix::settings()->get([]);
    expect($res)->toBeArray();
});
