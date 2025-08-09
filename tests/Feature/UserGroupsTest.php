<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists user groups', function () {
    $groups = Zabbix::userGroups()->get(['output' => ['usrgrpid', 'name']]);
    expect($groups)->toBeArray()->not->toBeEmpty();
});
