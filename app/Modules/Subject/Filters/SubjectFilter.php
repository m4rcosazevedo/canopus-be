<?php

namespace App\Modules\Subject\Filters;

use App\Builders\QueryFilter;

class SubjectFilter extends QueryFilter
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

    public function area($value): void
    {
        $value = (int) $value;
        $this->builder->whereHas('area', function ($query) use ($value) {
            $query->where('id', $value);
        });
    }
}
