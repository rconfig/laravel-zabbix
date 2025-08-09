<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists host groups', function () {
    $groups = Zabbix::hostGroups()->all(5);
    expect($groups)->toBeArray();
});
