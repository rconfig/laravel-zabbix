<?php

namespace Rconfig\Zabbix\Resources;

use Rconfig\Zabbix\Contracts\ZabbixClient;

class Problems
{
    public function __construct(protected ZabbixClient $client) {}

    public function get(array $params = []): array
    {
        return $this->client->call('problem.get', $params);
    }

    public function update(array $params): array
    {
        return $this->client->call('problem.update', $params);
    }
}
