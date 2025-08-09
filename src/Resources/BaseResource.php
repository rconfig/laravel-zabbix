<?php

// src/Resources/BaseResource.php

namespace Rconfig\Zabbix\Resources;

use InvalidArgumentException;
use Rconfig\Zabbix\Contracts\ZabbixClient;
use Rconfig\Zabbix\Exceptions\UnsupportedOperationException;

abstract class BaseResource
{
    public function __construct(protected ZabbixClient $client) {}

    abstract protected function base(): string;

    // Opt-in capability flags. Override in child resources.
    protected bool $supportsCreate = true;

    protected bool $supportsUpdate = true;

    protected bool $supportsDelete = true;

    /** @param array<string,mixed> $params */
    public function get(array $params = []): array
    {
        return $this->client->call($this->base().'.get', $params);
    }

    /** @param array<string,mixed> $payload */
    public function create(array $payload): array
    {
        if (! $this->supportsCreate) {
            throw new UnsupportedOperationException(static::class.' does not support create().');
        }

        return $this->client->call($this->base().'.create', $payload);
    }

    /** @param array<string,mixed> $payload */
    public function update(array $payload): array
    {
        if (! $this->supportsUpdate) {
            throw new UnsupportedOperationException(static::class.' does not support update().');
        }

        return $this->client->call($this->base().'.update', $payload);
    }

    /** @param array<int|string,mixed> $ids */
    public function delete(array $ids): array
    {
        if (! $this->supportsDelete) {
            throw new UnsupportedOperationException(static::class.' does not support delete().');
        }

        return $this->client->call($this->base().'.delete', $ids);
    }

    /**
     * Minimal param validator (opt-in via config zabbix.validate_params).
     * Example $rules: ['itemids' => 'array', 'history' => 'int']
     */
    protected function validate(array $params, array $rules): void
    {
        if (! config('zabbix.validate_params')) {
            return;
        }

        foreach ($rules as $key => $type) {
            if (! array_key_exists($key, $params)) {
                continue; // optional
            }
            $val = $params[$key];

            $ok = match ($type) {
                'array' => is_array($val),
                'int' => is_int($val) || (is_string($val) && ctype_digit($val)),
                'bool' => is_bool($val) || $val === 0 || $val === 1 || $val === '0' || $val === '1',
                'string' => is_string($val),
                default => true,
            };

            if (! $ok) {
                throw new InvalidArgumentException(sprintf(
                    '%s: param "%s" must be %s.',
                    static::class,
                    $key,
                    $type
                ));
            }
        }
    }
}
