<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('gets configuration', function () {
    $res = Zabbix::configurations()->get([]);
    expect($res)->toBeArray();
});
