<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists correlations', function () {
    $res = Zabbix::correlations()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
