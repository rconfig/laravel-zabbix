<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists SLAs', function () {
    $res = Zabbix::slas()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
