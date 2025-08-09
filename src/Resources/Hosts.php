<?php

namespace Rconfig\Zabbix\Resources;

use Rconfig\Zabbix\Contracts\ZabbixClient;
use Rconfig\Zabbix\Resources\Queries\HostQuery;

class Hosts
{
    public function __construct(protected ZabbixClient $client) {}

    public function query(): HostQuery
    {
        return new HostQuery;
    }

    public function get(HostQuery $q): array
    {
        return $this->client->call('host.get', $q->params());
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

    // Fluent convenience
    public function where(array $filter): array
    {
        return $this->get($this->query()->where($filter));
    }

    public function all(int $limit = 1000): array
    {
        return $this->get($this->query()->limit($limit));
    }
}
