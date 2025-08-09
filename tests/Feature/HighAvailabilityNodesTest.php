<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists HA nodes', function () {
    $res = Zabbix::highAvailabilityNodes()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
