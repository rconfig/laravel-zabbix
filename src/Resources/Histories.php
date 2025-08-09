<?php

// src/Resources/Histories.php

namespace Rconfig\Zabbix\Resources;

class Histories extends BaseResource
{
    protected function base(): string
    {
        return 'history';
    }

    // history.* is read-only (get only)
    protected bool $supportsCreate = false;

    protected bool $supportsUpdate = false;

    protected bool $supportsDelete = false;

    /** @param array<string,mixed> $params */
    public function get(array $params = []): array
    {
        // Zabbix expects: itemids (array), history (int), limit (int)...
        $this->validate($params, [
            'itemids' => 'array',
            'history' => 'int',
            'limit' => 'int',
        ]);

        return parent::get($params);
    }
}
