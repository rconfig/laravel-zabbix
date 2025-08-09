<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists template groups', function () {
    $res = Zabbix::templateGroups()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
