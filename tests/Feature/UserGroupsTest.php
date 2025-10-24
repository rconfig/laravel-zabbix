<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('lists user groups', function () {
    ZabbixApi::login();
    $groups = ZabbixApi::userGroups()->get(['output' => ['usrgrpid', 'name']]);
    expect($groups)->toBeArray()->not->toBeEmpty();
});
