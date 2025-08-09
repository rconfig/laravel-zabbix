<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('fetches hosts via fluent query', function () {
    $hosts = Zabbix::hosts()->get(
        Zabbix::hosts()->query()
            ->select(['hostid', 'host', 'status'])
            ->withInterfaces()
            ->withGroups()
            ->where(['status' => 0])
            ->limit(10)
    );

    expect($hosts)->toBeArray()->and($hosts)->not->toBeEmpty();
    expect($hosts[0])->toHaveKeys(['hostid', 'host']);
});

it('gets api version', function () {
    $ver = Zabbix::apiVersion();
    expect($ver)->toBeString()->and($ver)->toMatch('/^\d+\.\d+/');
});
