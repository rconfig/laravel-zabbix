<?php

// tests/Feature/ParamValidationTest.php

use Rconfig\Zabbix\Facades\Zabbix;

beforeEach(function () {
    // Enable validation for this test file regardless of .env
    config()->set('zabbix.validate_params', true);
});

it('validates history.get parameter types', function () {
    // itemids should be an array; history an int
    expect(fn () => Zabbix::histories()->get([
        'itemids' => 'wrong',
        'history' => 'x',
        'limit' => 'nope',
    ]))->toThrow(InvalidArgumentException::class);
});
