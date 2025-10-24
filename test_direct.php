<?php

require_once __DIR__ . '/vendor/autoload.php';

use Rconfig\Zabbix\ZabbixConnector;

echo "=== Testing ZabbixConnector directly ===" . PHP_EOL;

try {
    $connector = new ZabbixConnector();

    $result = $connector->login(
        'https://zabbix.dev.rconfig.com',
        'admin2',
        'zabbix1234',
        [
            'debug' => true,
            'sslVerifyPeer' => false,
            'sslVerifyHost' => false,
            'timeout' => 10,
        ]
    );

    echo "✅ Login successful!" . PHP_EOL;

    // Test a simple API call
    $hosts = $connector->hosts()->count();
    echo "✅ Total hosts: {$hosts}" . PHP_EOL;
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
    echo "❌ Code: " . $e->getCode() . PHP_EOL;
    echo "❌ File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}
