<?php

namespace Rconfig\Zabbix\Resources;

use Rconfig\Zabbix\Contracts\ZabbixClient;

class Users
{
    public function __construct(protected ZabbixClient $client) {}

    public function get(array $params = []): array
    {
        return $this->client->call('user.get', $params);
    }

    public function create(array $users): array
    {
        return $this->client->call('user.create', $users);
    }

    public function update(array $users): array
    {
        return $this->client->call('user.update', $users);
    }

    public function delete(array $userIds): array
    {
        return $this->client->call('user.delete', $userIds);
    }
}
