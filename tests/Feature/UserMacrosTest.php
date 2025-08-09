<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists user macros', function () {
    $res = Zabbix::userMacros()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
