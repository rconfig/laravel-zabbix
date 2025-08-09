<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists images', function () {
    $res = Zabbix::images()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
