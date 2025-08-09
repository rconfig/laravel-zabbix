<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('gets housekeeping', function () {
    $res = Zabbix::housekeeping()->get([]);
    expect($res)->toBeArray();
});
