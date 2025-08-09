<?php

namespace Rconfig\Zabbix\Resources;

class WebScenarios extends BaseResource
{
    // Zabbix API names web scenarios under "httptest.*"
    protected function base(): string
    {
        return 'httptest';
    }
}
