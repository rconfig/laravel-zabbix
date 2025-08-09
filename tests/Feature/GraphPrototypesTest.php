<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists graph prototypes', function () {
    $res = Zabbix::graphPrototypes()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
