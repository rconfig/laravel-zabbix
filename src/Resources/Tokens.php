<?php

namespace Rconfig\Zabbix\Resources;

use Rconfig\Zabbix\Contracts\ZabbixClient;

class Tokens
{
    public function __construct(protected ZabbixClient $client) {}

    public function get(array $params = []): array
    {
        return $this->client->call('token.get', $params);
    }

    public function create(array $params): array
    {
        return $this->client->call('token.create', $params);
    }

    public function revoke(array $params): array
    {
        return $this->client->call('token.revoke', $params);
    }

    public function delete(array $tokenIds): array
    {
        return $this->client->call('token.delete', $tokenIds);
    }
}
