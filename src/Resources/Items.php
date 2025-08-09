<?php

namespace Rconfig\Zabbix\Resources;

use Rconfig\Zabbix\Contracts\ZabbixClient;

class Items
{
    public function __construct(protected ZabbixClient $client) {}

    public function get(array $params = []): array
    {
        return $this->client->call('item.get', $params);
    }

    public function create(array $items): array
    {
        return $this->client->call('item.create', $items);
    }

    public function update(array $items): array
    {
        return $this->client->call('item.update', $items);
    }

    public function delete(array $itemIds): array
    {
        return $this->client->call('item.delete', $itemIds);
    }
}
