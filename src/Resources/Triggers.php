<?php

namespace Rconfig\Zabbix\Resources;

use Rconfig\Zabbix\Contracts\ZabbixClient;

class Triggers
{
    public function __construct(protected ZabbixClient $client) {}

    public function get(array $params = []): array
    {
        return $this->client->call('trigger.get', $params);
    }

    public function create(array $triggers): array
    {
        return $this->client->call('trigger.create', $triggers);
    }

    public function update(array $triggers): array
    {
        return $this->client->call('trigger.update', $triggers);
    }

    public function delete(array $triggerIds): array
    {
        return $this->client->call('trigger.delete', $triggerIds);
    }

    public function acknowledge(array $params): array
    {
        return $this->client->call('trigger.acknowledge', $params);
    }
}
