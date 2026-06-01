<?php

namespace Rconfig\Zabbix;

use Illuminate\Support\ServiceProvider;

class ZabbixServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/zabbix.php', 'zabbix');

        // Register the ZabbixConnector
        $this->app->singleton(ZabbixConnector::class, function ($app) {
            return new ZabbixConnector;
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/zabbix.php' => config_path('zabbix.php'),
        ], 'zabbix-config');
    }
}
