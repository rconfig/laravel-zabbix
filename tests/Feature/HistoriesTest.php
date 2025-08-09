<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('gets histories', function () {
    $res = Zabbix::histories()->get(['itemids' => ['30001'], 'history' => 0, 'limit' => 1]);
    expect($res)->toBeArray();
});
