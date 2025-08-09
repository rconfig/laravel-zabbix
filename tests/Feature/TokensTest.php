<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('gets tokens (fake)', function () {
    $t = Zabbix::tokens()->get(['output' => 'extend', 'limit' => 2]);
    expect($t)->toBeArray();
});
