<?php

namespace Rconfig\Zabbix\Resources;

use Rconfig\Zabbix\Contracts\ZabbixClient;
use Rconfig\Zabbix\Resources\Queries\HostQuery;

class Hosts
{
    protected HostQuery $currentQuery;

    public function __construct(protected ZabbixClient $client)
    {
        $this->currentQuery = new HostQuery;
    }

    public function query(): HostQuery
    {
        return new HostQuery;
    }

    public function get(?HostQuery $q = null): array|string|int
    {
        $query = $q ?? $this->currentQuery;
        $result = $this->client->call('host.get', $query->params());

        // If countOutput is set, return the count as-is (string/int)
        if (isset($query->params()['countOutput']) && $query->params()['countOutput']) {
            return $result;
        }

        // Otherwise ensure we return an array
        return is_array($result) ? $result : [];
    }

    public function create(array $host): array
    {
        return $this->client->call('host.create', $host);
    }

    public function update(array $host): array
    {
        return $this->client->call('host.update', $host);
    }

    public function delete(array $hostIds): array
    {
        return $this->client->call('host.delete', $hostIds);
    }

    // Fluent convenience (legacy support)
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

    public function filter(array $filter): static
    {
        $this->currentQuery->where($filter);

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

    public function withInterfaces(): static
    {
        $this->currentQuery->withInterfaces();

        return $this;
    }

    public function withGroups(): static
    {
        $this->currentQuery->withGroups();

        return $this;
    }

    public function byIds(array $ids): static
    {
        $this->currentQuery->byIds($ids);

        return $this;
    }

    public function inGroupIds(array $groupIds): static
    {
        $this->currentQuery->inGroupIds($groupIds);

        return $this;
    }

    public function countOnly(): static
    {
        $this->currentQuery->countOnly();

        return $this;
    }

    public function count(): int|string
    {
        return $this->countOnly()->get();
    }
}
