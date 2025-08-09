<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('gets trends', function () {
    $res = Zabbix::trends()->get(['itemids' => ['30001'], 'limit' => 1]);
    expect($res)->toBeArray();
});
