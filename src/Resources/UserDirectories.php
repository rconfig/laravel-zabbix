<?php

namespace Rconfig\Zabbix\Resources;

class UserDirectories extends BaseResource
{
    protected function base(): string
    {
        return 'userdirectory';
    }
}
