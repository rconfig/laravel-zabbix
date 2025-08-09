<?php

namespace Rconfig\Zabbix\Resources;

class Alerts extends BaseResource
{
    protected function base(): string
    {
        return 'alert';
    }
}
