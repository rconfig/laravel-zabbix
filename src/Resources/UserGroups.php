<?php

namespace Rconfig\Zabbix\Resources;

use Rconfig\Zabbix\Contracts\ZabbixClient;

class UserGroups
{
    public function __construct(protected ZabbixClient $client) {}

    public function get(array $params = []): array
    {
        return $this->client->call('usergroup.get', $params);
    }

    public function create(array $groups): array
    {
        return $this->client->call('usergroup.create', $groups);
    }

    public function update(array $groups): array
    {
        return $this->client->call('usergroup.update', $groups);
    }

    public function delete(array $groupIds): array
    {
        return $this->client->call('usergroup.delete', $groupIds);
    }
}
