<?php

use Rconfig\Zabbix\Exceptions\ZabbixException;
use Rconfig\Zabbix\Facades\ZabbixApi;
use Rconfig\Zabbix\ZabbixConnector;

it('can create ZabbixConnector instance', function () {
    $zabbix = new ZabbixConnector;
    expect($zabbix)->toBeInstanceOf(ZabbixConnector::class);
    expect($zabbix->isLoggedIn())->toBeFalse();
});

it('can login with config fallback', function () {
    // This test will use the config values (which should use fakes)
    $zabbix = new ZabbixConnector;

    // Login should work with config fallback
    $zabbix->login();

    expect($zabbix->isLoggedIn())->toBeTrue();
    expect($zabbix->apiVersion())->toBeString();
});

it('can use facade with login', function () {
    // Login first
    ZabbixApi::login();

    expect(ZabbixApi::isLoggedIn())->toBeTrue();

    // Now test API calls
    $version = ZabbixApi::apiVersion();
    expect($version)->toBeString();

    // Test resource access
    $hosts = ZabbixApi::hosts()->all(5);
    expect($hosts)->toBeArray();
});

it('throws exception when not logged in', function () {
    $zabbix = new ZabbixConnector;

    // Should throw exception when trying to use API without login
    expect(fn () => $zabbix->hosts())
        ->toThrow(ZabbixException::class, 'Not logged in');
});

it('can logout', function () {
    ZabbixApi::login();
    expect(ZabbixApi::isLoggedIn())->toBeTrue();

    ZabbixApi::logout();
    expect(ZabbixApi::isLoggedIn())->toBeFalse();
});

it('can login with options', function () {
    $options = [
        'debug' => false,
        'timeout' => 60,
        'connectTimeout' => 30,
        'sslVerifyPeer' => false,
        'sslVerifyHost' => false,
        'useGzip' => true,
        'retries' => 5,
        'retry_sleep_ms' => 1000,
    ];

    ZabbixApi::login(null, null, null, $options);

    expect(ZabbixApi::isLoggedIn())->toBeTrue();
    expect(ZabbixApi::getTimeout())->toBe(60);
    expect(ZabbixApi::getConnectTimeout())->toBe(30);
    expect(ZabbixApi::isDebugEnabled())->toBeFalse();
    expect(ZabbixApi::isGzipEnabled())->toBeTrue();

    $sslSettings = ZabbixApi::getSslSettings();
    expect($sslSettings['sslVerifyPeer'])->toBe(0);
    expect($sslSettings['sslVerifyHost'])->toBe(0);
});

it('can login with debug enabled', function () {
    $options = ['debug' => true];

    // Capture output
    ob_start();
    ZabbixApi::login(null, null, null, $options);
    $output = ob_get_clean();

    expect(ZabbixApi::isDebugEnabled())->toBeTrue();
});

it('validates invalid options', function () {
    $options = ['invalidOption' => 'value'];

    expect(fn () => ZabbixApi::login(null, null, null, $options))
        ->toThrow(ZabbixException::class, 'Invalid option used');
});

it('validates ssl ca file exists', function () {
    $options = ['sslCaFile' => '/non/existent/file.crt'];

    expect(fn () => ZabbixApi::login(null, null, null, $options))
        ->toThrow(ZabbixException::class, 'is not a valid file');
});

it('can login with token via options', function () {
    $options = ['token' => 'fake-bearer-token'];

    ZabbixApi::login('https://example.com', null, null, $options);

    expect(ZabbixApi::isLoggedIn())->toBeTrue();
});

it('can use loginWithToken method', function () {
    ZabbixApi::loginWithToken('https://example.com', 'fake-bearer-token');

    expect(ZabbixApi::isLoggedIn())->toBeTrue();
});

it('handles timeout options correctly', function () {
    $options = [
        'timeout' => 0,        // Should default to 30
        'connectTimeout' => -5, // Should default to 30
        'retries' => -1,       // Should default to 2
        'retry_sleep_ms' => -100, // Should default to 250
    ];

    ZabbixApi::login(null, null, null, $options);

    expect(ZabbixApi::getTimeout())->toBe(30);
    expect(ZabbixApi::getConnectTimeout())->toBe(30);
});

it('can get ssl settings', function () {
    $options = [
        'sslVerifyPeer' => true,
        'sslVerifyHost' => true,
    ];

    ZabbixApi::login(null, null, null, $options);

    $sslSettings = ZabbixApi::getSslSettings();
    expect($sslSettings)->toBeArray();
    expect($sslSettings)->toHaveKeys(['sslVerifyPeer', 'sslVerifyHost', 'sslCaFile']);
    expect($sslSettings['sslVerifyPeer'])->toBe(1);
    expect($sslSettings['sslVerifyHost'])->toBe(2);
});
