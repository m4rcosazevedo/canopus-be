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

    public function ibgeCode($code)
    {
        $abbr = (int) $code;
        return $this->builder->where('ibge_code', '=', $code);
    }
}
