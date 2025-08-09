<?php

namespace Rconfig\Zabbix;

use Illuminate\Support\ServiceProvider;
use Rconfig\Zabbix\Contracts\ZabbixClient;
use Rconfig\Zabbix\Http\JsonRpcClient;

class ZabbixServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/zabbix.php', 'zabbix');

        $this->app->singleton(ZabbixClient::class, function ($app) {
            $cfg = $app['config']->get('zabbix');

            $hasBearer = ! empty($cfg['token']);

            return new JsonRpcClient(
                baseUrl: rtrim($cfg['base_url'], '/'),
                endpoint: $cfg['endpoint'],
                username: $cfg['username'],
                password: $cfg['password'],
                hasBearerToken: $hasBearer,
                timeout: (int) $cfg['timeout'],
                retries: (int) $cfg['retries'],
                retrySleepMs: (int) $cfg['retry_sleep_ms'],
                bearer: $hasBearer ? $cfg['token'] : null,
            );
        });

        $this->app->singleton(ZabbixManager::class, fn ($app) => new ZabbixManager($app->make(ZabbixClient::class)));
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/zabbix.php' => config_path('zabbix.php'),
        ], 'zabbix-config');
    }
}
