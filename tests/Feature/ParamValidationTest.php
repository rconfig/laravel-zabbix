<?php

// tests/Feature/ParamValidationTest.php

use Rconfig\Zabbix\Facades\ZabbixApi;

beforeEach(function () {
    // Enable validation for this test file regardless of .env
    config()->set('zabbix.validate_params', true);
});

it('validates history.get parameter types', function () {
    ZabbixApi::login();
    // itemids should be an array; history an int
    expect(fn () => ZabbixApi::histories()->get([
        'itemids' => 'wrong',
        'history' => 'x',
        'limit' => 'nope',
    ]))->toThrow(InvalidArgumentException::class);
});
