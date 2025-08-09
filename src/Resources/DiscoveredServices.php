<?php

namespace Rconfig\Zabbix\Resources;

class DiscoveredServices extends BaseResource
{
    protected function base(): string
    {
        return 'discoveredservice';
    }
}
