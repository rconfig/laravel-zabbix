<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists discovery checks', function () {
    $res = Zabbix::discoveryChecks()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
