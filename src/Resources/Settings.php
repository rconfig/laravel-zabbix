<?php

// src/Resources/Settings.php

namespace Rconfig\Zabbix\Resources;

class Settings extends BaseResource
{
    protected function base(): string
    {
        return 'settings';
    }

    // allow update, but no create/delete
    protected bool $supportsCreate = false;

    protected bool $supportsDelete = false;
}
