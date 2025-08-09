<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists proxy groups', function () {
    $res = Zabbix::proxyGroups()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
