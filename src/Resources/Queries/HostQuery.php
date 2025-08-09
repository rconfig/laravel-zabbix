<?php

namespace Rconfig\Zabbix\Resources\Queries;

use Rconfig\Zabbix\Support\FluentQuery;

class HostQuery extends FluentQuery
{
    public function byIds(array $ids): static
    {
        $this->params['hostids'] = $ids;

        return $this;
    }

    public function inGroupIds(array $groupIds): static
    {
        $this->params['groupids'] = $groupIds;

        return $this;
    }

    public function withInterfaces(): static
    {
        $this->params['selectInterfaces'] = 'extend';

        return $this;
    }

    public function withGroups(): static
    {
        $this->params['selectGroups'] = 'extend';

        return $this;
    }
}
