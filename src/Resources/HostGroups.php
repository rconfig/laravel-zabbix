<?php

namespace Rconfig\Zabbix\Resources;

use Rconfig\Zabbix\Contracts\ZabbixClient;
use Rconfig\Zabbix\Resources\Queries\HostGroupQuery;

class HostGroups
{
    protected HostGroupQuery $currentQuery;

    public function __construct(protected ZabbixClient $client)
    {
        $this->currentQuery = new HostGroupQuery;
    }

    public function query(): HostGroupQuery
    {
        return new HostGroupQuery;
    }

    public function get(?HostGroupQuery $q = null): array
    {
        $query = $q ?? $this->currentQuery;

        return $this->client->call('hostgroup.get', $query->params());
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

    // New fluent API - delegate to current query and return self
    public function limit(int $limit): static
    {
        $this->currentQuery->limit($limit);

        return $this;
    }

    public function select(array $fields): static
    {
        $this->currentQuery->select($fields);

        return $this;
    }

    public function where(array $filter): static
    {
        $this->currentQuery->where($filter);

        return $this;
    }

    public function sort(string $field, string $order = 'ASC'): static
    {
        $this->currentQuery->sort($field, $order);

        return $this;
    }

    public function byIds(array $ids): static
    {
        $this->currentQuery->byIds($ids);

        return $this;
    }
}
