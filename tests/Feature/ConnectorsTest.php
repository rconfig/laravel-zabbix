<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists connectors', function () {
    $res = Zabbix::connectors()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
