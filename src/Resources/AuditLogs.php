<?php

// src/Resources/AuditLogs.php

namespace Rconfig\Zabbix\Resources;

class AuditLogs extends BaseResource
{
    protected function base(): string
    {
        return 'auditlog';
    }

    // auditlog.* is read-only
    protected bool $supportsCreate = false;

    protected bool $supportsUpdate = false;

    protected bool $supportsDelete = false;
}
