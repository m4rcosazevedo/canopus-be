<?php

namespace App\Modules\UserType\Filters;

use App\Builders\QueryFilter;

class UserTypeFilter extends QueryFilter
{
    public function name(string $value): void
    {
        $this->builder->where('name', 'LIKE', "%$value%");
    }

    public function description(string $value): void
    {
        $this->builder->where('description', 'LIKE', "%$value%");
    }

    public function visible($value): void
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $this->builder->where('visible', $value);
    }
}
