<?php

namespace Rconfig\Zabbix\Resources;

class Services extends BaseResource
{
    protected function base(): string
    {
        return 'service';
    }
}
