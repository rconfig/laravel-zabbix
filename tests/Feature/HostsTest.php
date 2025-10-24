<?php

use Rconfig\Zabbix\Facades\ZabbixApi;

it('fetches hosts via fluent query', function () {
    ZabbixApi::login();
    $hosts = ZabbixApi::hosts()->get(
        ZabbixApi::hosts()->query()
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
    ZabbixApi::login();
    $ver = ZabbixApi::apiVersion();
    expect($ver)->toBeString()->and($ver)->toMatch('/^\d+\.\d+/');
});
