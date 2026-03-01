<?php

namespace App\Modules\Area\Filters;

use App\Builders\QueryFilter;

class AreaFilter extends QueryFilter
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
}
