<?php

namespace Rconfig\Zabbix\Support;

abstract class FluentQuery
{
    protected array $params = [];

    public function select(array $fields): static
    {
        $this->params['output'] = $fields;

        return $this;
    }

    public function with(array $relations): static
    {
        foreach ($relations as $rel) {
            $this->params[$rel.'_output'] = 'extend';
        }

        return $this;
    }

    public function where(array $filter): static
    {
        $this->params['filter'] = array_merge($this->params['filter'] ?? [], $filter);

        return $this;
    }

    public function search(array $search): static
    {
        $this->params['search'] = $search;

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->params['limit'] = $limit;

        return $this;
    }

    public function sort(string $field, string $order = 'ASC'): static
    {
        $this->params['sortfield'] = $field;
        $this->params['sortorder'] = strtoupper($order);

        return $this;
    }

    public function params(): array
    {
        return $this->params;
    }
}
