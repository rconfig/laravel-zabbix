<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists items', function () {
    $items = Zabbix::items()->get(['hostids' => ['10106'], 'output' => ['itemid', 'name']]);
    expect($items)->toBeArray()->not->toBeEmpty();
});
