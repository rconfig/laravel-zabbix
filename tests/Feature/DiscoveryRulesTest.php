<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists discovery rules', function () {
    $res = Zabbix::discoveryRules()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
