<?php

// src/Resources/Trends.php

namespace Rconfig\Zabbix\Resources;

class Trends extends BaseResource
{
    protected function base(): string
    {
        return 'trend';
    }

    // trend.* is read-only (get only)
    protected bool $supportsCreate = false;

    protected bool $supportsUpdate = false;

    protected bool $supportsDelete = false;

    public function get(array $params = []): array
    {
        $this->validate($params, [
            'itemids' => 'array',
            'limit' => 'int',
        ]);

        return parent::get($params);
    }
}
