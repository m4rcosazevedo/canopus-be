<?php

namespace App\Modules\State\Filters;

use App\Builders\QueryFilter;

class StateFilter extends QueryFilter
{
    public function name(string $name)
    {
        return $this->builder->where('name', 'LIKE', '%' . $name . '%');
    }

    public function abbr($abbr)
    {
        return $this->builder->where('abbr', '=', $abbr);
    }
}
