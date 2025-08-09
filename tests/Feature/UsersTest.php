<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists users', function () {
    $u = Zabbix::users()->get(['output' => ['userid', 'username']]);
    expect($u)->toBeArray();
});
