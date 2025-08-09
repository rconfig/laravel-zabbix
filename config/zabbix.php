<?php

return [
    'base_url' => env('ZABBIX_BASE_URL', 'http://localhost'),
    'endpoint' => env('ZABBIX_ENDPOINT', '/api_jsonrpc.php'),

    // Prefer API token (Zabbix 5.4+) via Authorization: Bearer
    'token' => env('ZABBIX_API_TOKEN', null),

    // Optional legacy auth fallback
    'username' => env('ZABBIX_USERNAME', null),
    'password' => env('ZABBIX_PASSWORD', null),

    // HTTP / Retry
    'timeout' => (int) env('ZABBIX_TIMEOUT', 15),
    'retries' => (int) env('ZABBIX_RETRIES', 2),
    'retry_sleep_ms' => (int) env('ZABBIX_RETRY_SLEEP_MS', 250),

    // Testing mode: when true, uses Http::fake unless RUN_ZABBIX_INTEGRATION=1
    'fake_by_default' => env('ZABBIX_FAKE_BY_DEFAULT', true),

    'validate_params' => (bool) env('ZABBIX_VALIDATE_PARAMS', false),
];
