<?php

namespace Rconfig\Zabbix\Exceptions;

use RuntimeException;
use Throwable;

class ZabbixHttpException extends RuntimeException
{
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
