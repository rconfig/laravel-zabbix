<?php

namespace Rconfig\Zabbix\Resources;

class Configurations extends BaseResource
{
    protected function base(): string
    {
        return 'configuration';
    }
}
