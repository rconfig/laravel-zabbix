<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists proxies', function () {
    $res = Zabbix::proxies()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
