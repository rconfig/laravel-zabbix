<?php

// tests/Feature/UnsupportedOperationsTest.php

use Rconfig\Zabbix\Exceptions\UnsupportedOperationException;
use Rconfig\Zabbix\Facades\ZabbixApi;

it('prevents create/update/delete on histories', function () {
    ZabbixApi::login();
    expect(fn () => ZabbixApi::histories()->create([]))
        ->toThrow(UnsupportedOperationException::class);

    expect(fn () => ZabbixApi::histories()->update([]))
        ->toThrow(UnsupportedOperationException::class);

    expect(fn () => ZabbixApi::histories()->delete([]))
        ->toThrow(UnsupportedOperationException::class);
});

it('prevents create/delete on settings', function () {
    ZabbixApi::login();
    expect(fn () => ZabbixApi::settings()->create(['some' => 'value']))
        ->toThrow(UnsupportedOperationException::class);

    expect(fn () => ZabbixApi::settings()->delete(['id']))
        ->toThrow(UnsupportedOperationException::class);
});
