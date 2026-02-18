<?php

namespace App\Modules\City\Filters;

use App\Builders\QueryFilter;

class CityFilter extends QueryFilter
{
    public function name(string $name)
    {
        return $this->builder->where('name', 'LIKE', "%$name%");
    }

    public function stateId($stateId)
    {
        $stateId = (int) $stateId;
        return $this->builder->where('state_id', '=', $stateId);
    }
}
