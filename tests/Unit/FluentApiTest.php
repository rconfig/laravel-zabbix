<?php

namespace Tests\Unit;

use Rconfig\Zabbix\Facades\ZabbixApi;

it('supports new fluent API syntax', function () {
    $zabbix = ZabbixApi::login('fake://localhost');

    // Test the new fluent API
    $hosts = $zabbix->hosts()
        ->limit(5)
        ->withGroups()
        ->withInterfaces()
        ->get();

    expect($hosts)->toBeArray();
    expect(count($hosts))->toBeLessThanOrEqual(5);
});

it('supports legacy query builder API', function () {
    $zabbix = ZabbixApi::login('fake://localhost');

    // Test the legacy explicit query builder API
    $hosts = $zabbix->hosts()->get(
        $zabbix->hosts()->query()
            ->limit(5)
            ->withGroups()
            ->withInterfaces()
    );

    expect($hosts)->toBeArray();
    expect(count($hosts))->toBeLessThanOrEqual(5);
});

it('supports host groups fluent API', function () {
    $zabbix = ZabbixApi::login('fake://localhost');

    // Test host groups fluent API
    $groups = $zabbix->hostGroups()
        ->limit(3)
        ->select(['groupid', 'name'])
        ->get();

    expect($groups)->toBeArray();
    expect(count($groups))->toBeLessThanOrEqual(3);
});

it('supports where method in fluent API', function () {
    $zabbix = ZabbixApi::login('fake://localhost');

    // Test where clause in fluent API
    $hosts = $zabbix->hosts()
        ->where(['status' => 0])
        ->limit(3)
        ->get();

    expect($hosts)->toBeArray();
});

it('supports count methods', function () {
    $zabbix = ZabbixApi::login('fake://localhost');

    // Test countOnly() method
    $hostCount1 = $zabbix->hosts()->countOnly()->get();
    expect($hostCount1)->toBeString();

    // Test shorthand count() method
    $hostCount2 = $zabbix->hosts()->count();
    expect($hostCount2)->toBeString();
});
