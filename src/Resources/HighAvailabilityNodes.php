<?php

namespace Rconfig\Zabbix\Resources;

class HighAvailabilityNodes extends BaseResource
{
    protected function base(): string
    {
        return 'hanode';
    }
}
