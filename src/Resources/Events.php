<?php

namespace Rconfig\Zabbix\Resources;

class Events extends BaseResource
{
    protected function base(): string
    {
        return 'event';
    }
}
