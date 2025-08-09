<?php

namespace Rconfig\Zabbix\Resources;

class DiscoveredHosts extends BaseResource
{
    protected function base(): string
    {
        return 'discoveredhost';
    }
}
