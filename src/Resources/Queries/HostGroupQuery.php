<?php

namespace Rconfig\Zabbix\Resources\Queries;

use Rconfig\Zabbix\Support\FluentQuery;

class HostGroupQuery extends FluentQuery
{
    public function byIds(array $ids): static
    {
        $this->params['groupids'] = $ids;

        return $this;
    }
}
