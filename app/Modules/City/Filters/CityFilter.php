<?php

namespace App\Modules\City\Filters;

use App\Builders\QueryFilter;

class CityFilter extends QueryFilter
{
    public function name(string $name)
    {
        return $this->builder->where('name', 'LIKE', "%$name%");
    }

    public function ibgeCode($code)
    {
        $code = (int) $code;
        return $this->builder->where('ibge_code', '=', $code);
    }

    public function stateId($stateId)
    {
        $stateId = (int) $stateId;
        return $this->builder->where('state_id', '=', $stateId);
    }
}
