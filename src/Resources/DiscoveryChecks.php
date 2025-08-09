<?php

namespace Rconfig\Zabbix\Resources;

class DiscoveryChecks extends BaseResource
{
    protected function base(): string
    {
        return 'discoverycheck';
    }
}
