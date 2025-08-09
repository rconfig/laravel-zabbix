<?php

namespace Rconfig\Zabbix\Resources;

use Rconfig\Zabbix\Contracts\ZabbixClient;

class Maintenances
{
    public function __construct(protected ZabbixClient $client) {}

    public function get(array $params = []): array
    {
        return $this->client->call('maintenance.get', $params);
    }

    public function create(array $payload): array
    {
        return $this->client->call('maintenance.create', $payload);
    }

    public function update(array $payload): array
    {
        return $this->client->call('maintenance.update', $payload);
    }

    public function delete(array $maintenanceIds): array
    {
        return $this->client->call('maintenance.delete', $maintenanceIds);
    }
}
