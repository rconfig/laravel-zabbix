<?php

use Illuminate\Support\Facades\Http;
use Rconfig\Zabbix\Exceptions\ZabbixException;
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

// ============================================================================
// Endpoint Discovery Tests (Zabbix 7.2+ URL checking)
// ============================================================================

it('builds correct endpoint variations for root URL', function () {
    $client = new JsonRpcClient(
        baseUrl: 'https://example.com',
        endpoint: '/api_jsonrpc.php'
    );

    $reflection = new ReflectionClass($client);
    $method = $reflection->getMethod('buildEndpointVariations');
    $method->setAccessible(true);

    $variations = $method->invoke($client);

    expect($variations)->toContain('https://example.com/api_jsonrpc.php')
        ->and($variations)->toContain('https://example.com/zabbix/api_jsonrpc.php')
        ->and($variations)->toBeArray();
});

it('builds correct endpoint variations for URL with /zabbix path', function () {
    $client = new JsonRpcClient(
        baseUrl: 'https://example.com/zabbix',
        endpoint: '/api_jsonrpc.php'
    );

    $reflection = new ReflectionClass($client);
    $method = $reflection->getMethod('buildEndpointVariations');
    $method->setAccessible(true);

    $variations = $method->invoke($client);

    expect($variations)->toContain('https://example.com/zabbix/api_jsonrpc.php')
        ->and($variations)->toBeArray();
});

it('builds correct endpoint variations for URL with custom path', function () {
    $client = new JsonRpcClient(
        baseUrl: 'https://example.com/monitoring',
        endpoint: '/api_jsonrpc.php'
    );

    $reflection = new ReflectionClass($client);
    $method = $reflection->getMethod('buildEndpointVariations');
    $method->setAccessible(true);

    $variations = $method->invoke($client);

    expect($variations)->toContain('https://example.com/monitoring/api_jsonrpc.php')
        ->and($variations)->toContain('https://example.com/zabbix/api_jsonrpc.php')
        ->and($variations)->toBeArray();
});

it('builds correct endpoint variations for URL with port', function () {
    $client = new JsonRpcClient(
        baseUrl: 'https://example.com:8443',
        endpoint: '/api_jsonrpc.php'
    );

    $reflection = new ReflectionClass($client);
    $method = $reflection->getMethod('buildEndpointVariations');
    $method->setAccessible(true);

    $variations = $method->invoke($client);

    expect($variations)->toContain('https://example.com:8443/api_jsonrpc.php')
        ->and($variations)->toContain('https://example.com:8443/zabbix/api_jsonrpc.php')
        ->and($variations)->toBeArray();
});

it('builds correct endpoint variations for full api_jsonrpc.php URL', function () {
    $client = new JsonRpcClient(
        baseUrl: 'https://example.com/zabbix/api_jsonrpc.php',
        endpoint: '/api_jsonrpc.php'
    );

    $reflection = new ReflectionClass($client);
    $method = $reflection->getMethod('buildEndpointVariations');
    $method->setAccessible(true);

    $variations = $method->invoke($client);

    // Should prioritize the exact URL provided
    expect($variations[0])->toBe('https://example.com/zabbix/api_jsonrpc.php')
        ->and($variations)->toBeArray();
});

it('discovers endpoint successfully with modern Zabbix 7.2 root endpoint', function () {
    Http::fake([
        'https://zabbix72.example.com/api_jsonrpc.php' => Http::response([
            'jsonrpc' => '2.0',
            'result' => '7.2.0',
            'id' => 1,
        ], 200),
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://zabbix72.example.com',
        endpoint: '/api_jsonrpc.php'
    );

    $endpoint = $client->getFullEndpointUrl();

    expect($endpoint)->toBe('https://zabbix72.example.com/api_jsonrpc.php');
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('discovers endpoint by trying /zabbix path when root fails', function () {
    Http::fake([
        'https://legacy.example.com/api_jsonrpc.php' => Http::response('Not Found', 404),
        'https://legacy.example.com/zabbix/api_jsonrpc.php' => Http::response([
            'jsonrpc' => '2.0',
            'result' => '6.0.0',
            'id' => 1,
        ], 200),
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://legacy.example.com',
        endpoint: '/api_jsonrpc.php'
    );

    $endpoint = $client->getFullEndpointUrl();

    expect($endpoint)->toBe('https://legacy.example.com/zabbix/api_jsonrpc.php');
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('throws exception when no endpoint can be discovered', function () {
    Http::fake([
        '*' => Http::response('Not Found', 404),
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://nonexistent.example.com',
        endpoint: '/api_jsonrpc.php'
    );

    expect(fn () => $client->getFullEndpointUrl())
        ->toThrow(ZabbixException::class, 'Could not discover Zabbix API endpoint');
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('caches discovered endpoint to avoid multiple discovery attempts', function () {
    Http::fake([
        'https://cache-test.example.com/api_jsonrpc.php' => Http::response([
            'jsonrpc' => '2.0',
            'result' => '7.2.0',
            'id' => 1,
        ], 200),
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://cache-test.example.com',
        endpoint: '/api_jsonrpc.php'
    );

    Http::recorded(); // Clear previous recordings

    $endpoint1 = $client->getFullEndpointUrl();
    $endpoint2 = $client->getFullEndpointUrl();

    expect($endpoint1)->toBe($endpoint2)
        ->and($endpoint1)->toBe('https://cache-test.example.com/api_jsonrpc.php');

    // Should only make one HTTP request for discovery
    $recorded = Http::recorded();
    expect(count($recorded))->toBe(1);
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

// ============================================================================
// API Version Detection Tests (Zabbix 7.2+ auth detection)
// ============================================================================

it('detects API version 7.2 and enables header auth', function () {
    Http::fake([
        'https://v72.example.com/api_jsonrpc.php' => Http::response([
            'jsonrpc' => '2.0',
            'result' => '7.2.0',
            'id' => 1,
        ], 200),
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://v72.example.com',
        endpoint: '/api_jsonrpc.php'
    );

    $version = $client->getApiVersion();
    $info = $client->getEndpointInfo();

    expect($version)->toBe('7.2.0')
        ->and($info['uses_header_auth'])->toBeTrue();
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('detects API version 7.0 and enables header auth', function () {
    Http::fake([
        'https://v70.example.com/api_jsonrpc.php' => Http::response([
            'jsonrpc' => '2.0',
            'result' => '7.0.0',
            'id' => 1,
        ], 200),
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://v70.example.com',
        endpoint: '/api_jsonrpc.php'
    );

    $version = $client->getApiVersion();
    $info = $client->getEndpointInfo();

    expect($version)->toBe('7.0.0')
        ->and($info['uses_header_auth'])->toBeTrue();
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('detects API version 6.4 and enables header auth', function () {
    Http::fake([
        'https://v64.example.com/api_jsonrpc.php' => Http::response([
            'jsonrpc' => '2.0',
            'result' => '6.4.0',
            'id' => 1,
        ], 200),
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://v64.example.com',
        endpoint: '/api_jsonrpc.php'
    );

    $version = $client->getApiVersion();
    $info = $client->getEndpointInfo();

    expect($version)->toBe('6.4.0')
        ->and($info['uses_header_auth'])->toBeTrue();
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('detects API version 6.0 and disables header auth', function () {
    Http::fake([
        'https://v60.example.com/api_jsonrpc.php' => Http::response([
            'jsonrpc' => '2.0',
            'result' => '6.0.0',
            'id' => 1,
        ], 200),
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://v60.example.com',
        endpoint: '/api_jsonrpc.php'
    );

    $version = $client->getApiVersion();
    $info = $client->getEndpointInfo();

    expect($version)->toBe('6.0.0')
        ->and($info['uses_header_auth'])->toBeFalse();
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('detects API version 5.4 and disables header auth', function () {
    Http::fake([
        'https://v54.example.com/api_jsonrpc.php' => Http::response([
            'jsonrpc' => '2.0',
            'result' => '5.4.0',
            'id' => 1,
        ], 200),
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://v54.example.com',
        endpoint: '/api_jsonrpc.php'
    );

    $version = $client->getApiVersion();
    $info = $client->getEndpointInfo();

    expect($version)->toBe('5.4.0')
        ->and($info['uses_header_auth'])->toBeFalse();
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('provides endpoint info including discovered endpoint and version', function () {
    Http::fake([
        'https://info.example.com/api_jsonrpc.php' => Http::response([
            'jsonrpc' => '2.0',
            'result' => '7.2.0',
            'id' => 1,
        ], 200),
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://info.example.com',
        endpoint: '/api_jsonrpc.php'
    );

    $info = $client->getEndpointInfo();

    expect($info)->toHaveKey('provided_url')
        ->and($info)->toHaveKey('discovered_endpoint')
        ->and($info)->toHaveKey('api_version')
        ->and($info)->toHaveKey('uses_header_auth')
        ->and($info['provided_url'])->toBe('https://info.example.com')
        ->and($info['discovered_endpoint'])->toBe('https://info.example.com/api_jsonrpc.php')
        ->and($info['api_version'])->toBe('7.2.0')
        ->and($info['uses_header_auth'])->toBeTrue();
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

// ============================================================================
// Authentication Method Tests (Header vs Body)
// ============================================================================

it('uses authorization header for Zabbix 7.2+ with session token', function () {
    $authHeaderUsed = false;
    $authInBodyUsed = false;

    Http::fake([
        'https://auth72.example.com/api_jsonrpc.php' => function ($request) use (&$authHeaderUsed, &$authInBodyUsed) {
            $body = json_decode($request->body(), true);

            if ($body['method'] === 'apiinfo.version') {
                return Http::response(['jsonrpc' => '2.0', 'result' => '7.2.0', 'id' => 1], 200);
            }

            if ($body['method'] === 'user.login') {
                return Http::response(['jsonrpc' => '2.0', 'result' => 'test-auth-token-72', 'id' => 1], 200);
            }

            if ($body['method'] === 'host.get') {
                $authHeaderUsed = $request->hasHeader('Authorization');
                $authInBodyUsed = isset($body['auth']);

                return Http::response(['jsonrpc' => '2.0', 'result' => [], 'id' => 1], 200);
            }

            return Http::response(['jsonrpc' => '2.0', 'result' => [], 'id' => 1], 200);
        },
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://auth72.example.com',
        endpoint: '/api_jsonrpc.php',
        username: 'admin',
        password: 'password'
    );

    $result = $client->call('host.get', ['output' => 'extend']);

    expect($result)->toBeArray()
        ->and($authHeaderUsed)->toBeTrue()
        ->and($authInBodyUsed)->toBeFalse();
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('uses auth in body for Zabbix 6.0 with session token', function () {
    $authHeaderUsed = false;
    $authInBodyUsed = false;
    $tokenValue = null;

    Http::fake([
        'https://auth60.example.com/api_jsonrpc.php' => function ($request) use (&$authHeaderUsed, &$authInBodyUsed, &$tokenValue) {
            $body = json_decode($request->body(), true);

            if ($body['method'] === 'apiinfo.version') {
                return Http::response(['jsonrpc' => '2.0', 'result' => '6.0.0', 'id' => 1], 200);
            }

            if ($body['method'] === 'user.login') {
                return Http::response(['jsonrpc' => '2.0', 'result' => 'test-auth-token-60', 'id' => 1], 200);
            }

            if ($body['method'] === 'host.get') {
                $authHeaderUsed = $request->hasHeader('Authorization');
                $authInBodyUsed = isset($body['auth']);
                $tokenValue = $body['auth'] ?? null;

                return Http::response(['jsonrpc' => '2.0', 'result' => [], 'id' => 1], 200);
            }

            return Http::response(['jsonrpc' => '2.0', 'result' => [], 'id' => 1], 200);
        },
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://auth60.example.com',
        endpoint: '/api_jsonrpc.php',
        username: 'admin',
        password: 'password'
    );

    $result = $client->call('host.get', ['output' => 'extend']);

    expect($result)->toBeArray()
        ->and($authHeaderUsed)->toBeFalse()
        ->and($authInBodyUsed)->toBeTrue()
        ->and($tokenValue)->toBe('test-auth-token-60');
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('uses bearer token in authorization header when configured', function () {
    $bearerTokenUsed = false;
    $authHeaderValue = null;

    Http::fake([
        'https://bearer.example.com/api_jsonrpc.php' => function ($request) use (&$bearerTokenUsed, &$authHeaderValue) {
            $body = json_decode($request->body(), true);

            if ($body['method'] === 'apiinfo.version') {
                return Http::response(['jsonrpc' => '2.0', 'result' => '7.2.0', 'id' => 1], 200);
            }

            if ($body['method'] === 'host.get') {
                $bearerTokenUsed = $request->hasHeader('Authorization');
                $authHeaderValue = $request->header('Authorization')[0] ?? null;

                return Http::response(['jsonrpc' => '2.0', 'result' => [], 'id' => 1], 200);
            }

            return Http::response(['jsonrpc' => '2.0', 'result' => [], 'id' => 1], 200);
        },
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://bearer.example.com',
        endpoint: '/api_jsonrpc.php',
        hasBearerToken: true,
        bearer: 'my-bearer-token-123'
    );

    $result = $client->call('host.get', ['output' => 'extend']);

    expect($result)->toBeArray()
        ->and($bearerTokenUsed)->toBeTrue()
        ->and($authHeaderValue)->toContain('Bearer my-bearer-token-123');
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('does not send auth for apiinfo.version method', function () {
    $authHeaderUsed = false;
    $authInBodyUsed = false;

    Http::fake([
        'https://noauth.example.com/api_jsonrpc.php' => function ($request) use (&$authHeaderUsed, &$authInBodyUsed) {
            $body = json_decode($request->body(), true);

            if ($body['method'] === 'apiinfo.version') {
                $authHeaderUsed = $request->hasHeader('Authorization');
                $authInBodyUsed = isset($body['auth']);

                return Http::response(['jsonrpc' => '2.0', 'result' => '7.2.0', 'id' => 1], 200);
            }

            return Http::response(['jsonrpc' => '2.0', 'result' => [], 'id' => 1], 200);
        },
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://noauth.example.com',
        endpoint: '/api_jsonrpc.php',
        username: 'admin',
        password: 'password'
    );

    $version = $client->getApiVersion();

    expect($version)->toBe('7.2.0')
        ->and($authHeaderUsed)->toBeFalse()
        ->and($authInBodyUsed)->toBeFalse();
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('does not send auth for user.login method', function () {
    $authHeaderUsedForLogin = false;

    Http::fake([
        'https://login.example.com/api_jsonrpc.php' => function ($request) use (&$authHeaderUsedForLogin) {
            $body = json_decode($request->body(), true);

            if ($body['method'] === 'apiinfo.version') {
                return Http::response(['jsonrpc' => '2.0', 'result' => '7.2.0', 'id' => 1], 200);
            }

            if ($body['method'] === 'user.login') {
                $authHeaderUsedForLogin = $request->hasHeader('Authorization');

                return Http::response(['jsonrpc' => '2.0', 'result' => 'test-token', 'id' => 1], 200);
            }

            return Http::response(['jsonrpc' => '2.0', 'result' => [], 'id' => 1], 200);
        },
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://login.example.com',
        endpoint: '/api_jsonrpc.php',
        username: 'admin',
        password: 'password'
    );

    $result = $client->call('host.get', ['output' => 'extend']);

    expect($result)->toBeArray()
        ->and($authHeaderUsedForLogin)->toBeFalse();
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

// ============================================================================
// URL Normalization and Edge Cases
// ============================================================================

it('handles URL with trailing slash correctly', function () {
    Http::fake([
        'https://slash.example.com/api_jsonrpc.php' => Http::response([
            'jsonrpc' => '2.0',
            'result' => '7.2.0',
            'id' => 1,
        ], 200),
    ]);

    $client = new JsonRpcClient(
        baseUrl: 'https://slash.example.com/',  // trailing slash
        endpoint: '/api_jsonrpc.php'
    );

    $endpoint = $client->getFullEndpointUrl();

    expect($endpoint)->toContain('https://slash.example.com');
})->skip(! env('RUN_ZABBIX_INTEGRATION'));

it('handles URL without scheme correctly', function () {
    $client = new JsonRpcClient(
        baseUrl: 'example.com',
        endpoint: '/api_jsonrpc.php'
    );

    $reflection = new ReflectionClass($client);
    $method = $reflection->getMethod('buildEndpointVariations');
    $method->setAccessible(true);

    $variations = $method->invoke($client);

    // Should handle gracefully even if scheme is missing
    expect($variations)->toBeArray()
        ->and(count($variations))->toBeGreaterThan(0);
});
