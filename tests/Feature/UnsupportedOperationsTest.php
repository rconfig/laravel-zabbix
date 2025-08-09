<?php

// tests/Feature/UnsupportedOperationsTest.php

use Rconfig\Zabbix\Exceptions\UnsupportedOperationException;
use Rconfig\Zabbix\Facades\Zabbix;

it('prevents create/update/delete on histories', function () {
    expect(fn () => Zabbix::histories()->create([]))
        ->toThrow(UnsupportedOperationException::class);

    expect(fn () => Zabbix::histories()->update([]))
        ->toThrow(UnsupportedOperationException::class);

    expect(fn () => Zabbix::histories()->delete([]))
        ->toThrow(UnsupportedOperationException::class);
});

it('prevents create/delete on settings', function () {
    expect(fn () => Zabbix::settings()->create(['some' => 'value']))
        ->toThrow(UnsupportedOperationException::class);

    expect(fn () => Zabbix::settings()->delete(['id']))
        ->toThrow(UnsupportedOperationException::class);
});
