<?php

namespace Rconfig\Zabbix\Resources;

use Rconfig\Zabbix\Contracts\ZabbixClient;
use Rconfig\Zabbix\Resources\Queries\HostGroupQuery;

class HostGroups
{
    public function __construct(protected ZabbixClient $client) {}

    public function query(): HostGroupQuery
    {
        return new HostGroupQuery;
    }

    public function get(HostGroupQuery $q): array
    {
        return $this->client->call('hostgroup.get', $q->params());
    }

    public function create(array $payload): array
    {
        return $this->client->call('hostgroup.create', $payload);
    }

    public function update(array $payload): array
    {
        return $this->client->call('hostgroup.update', $payload);
    }

    public function delete(array $groupIds): array
    {
        return $this->client->call('hostgroup.delete', $groupIds);
    }

    public function all(int $limit = 1000): array
    {
        return $this->get($this->query()->limit($limit));
    }
}
