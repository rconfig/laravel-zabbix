<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists LLD rules', function () {
    $res = Zabbix::lldRules()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
