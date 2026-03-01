<?php

namespace App\Modules\Plan\Filters;

use App\Builders\QueryFilter;

class PlanFilter extends QueryFilter
{
    public function id($id): void
    {
        $id = (int) $id;
        $this->builder->where('id', $id);
    }

    public function name(string $value): void
    {
        $this->builder->where('name', 'LIKE', "%$value%");
    }

    public function active($value): void
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $this->builder->where('active', $value);
    }
}
