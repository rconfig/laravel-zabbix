<?php

namespace Rconfig\Zabbix\DTOs;

class Response
{
    public function __construct(public array $data) {}

    public static function from(array $data): self
    {
        return new self($data);
    }
}
