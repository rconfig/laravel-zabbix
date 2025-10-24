<?php

use Rconfig\Zabbix\Http\JsonRpcClient;

it('can create JsonRpcClient with SSL options', function () {
    $sslOptions = [
        'verify' => false,
        'connect_timeout' => 30,
    ];

    $client = new JsonRpcClient(
        baseUrl: 'https://example.com',
        endpoint: '/api_jsonrpc.php',
        username: 'test',
        password: 'test',
        hasBearerToken: false,
        timeout: 15,
        retries: 2,
        retrySleepMs: 250,
        bearer: null,
        sslOptions: $sslOptions
    );

    expect($client)->toBeInstanceOf(JsonRpcClient::class);
});

it('can create JsonRpcClient with bearer token', function () {
    $client = new JsonRpcClient(
        baseUrl: 'https://example.com',
        endpoint: '/api_jsonrpc.php',
        username: null,
        password: null,
        hasBearerToken: true,
        timeout: 15,
        retries: 2,
        retrySleepMs: 250,
        bearer: 'fake-token',
        sslOptions: []
    );

    expect($client)->toBeInstanceOf(JsonRpcClient::class);
});

it('can create JsonRpcClient with default options', function () {
    $client = new JsonRpcClient(
        baseUrl: 'https://example.com',
        endpoint: '/api_jsonrpc.php'
    );

    expect($client)->toBeInstanceOf(JsonRpcClient::class);
});
