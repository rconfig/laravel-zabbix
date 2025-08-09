<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists value maps', function () {
    $res = Zabbix::valueMaps()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
