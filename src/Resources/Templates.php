<?php

namespace Rconfig\Zabbix\Resources;

use Rconfig\Zabbix\Contracts\ZabbixClient;

class Templates
{
    public function __construct(protected ZabbixClient $client) {}

    public function get(array $params = []): array
    {
        return $this->client->call('template.get', $params);
    }

    public function create(array $templates): array
    {
        return $this->client->call('template.create', $templates);
    }

    public function update(array $templates): array
    {
        return $this->client->call('template.update', $templates);
    }

    public function delete(array $templateIds): array
    {
        return $this->client->call('template.delete', $templateIds);
    }
}
