<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists web scenarios', function () {
    $res = Zabbix::webScenarios()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
