<?php

namespace App\Modules\City\Filters;

use App\Builders\SortableQueryFilter;

class CityFilter extends SortableQueryFilter
{
    protected array $sortable = [
        'id'        => 'id',
        'name'      => 'name',
        'ibgeCode'  => 'ibge_code',
        'state.name'     => 'state.name',
    ];

    protected string $sortColumn = 'name';

    protected string $sortOrder  = 'asc';

    public function id($id)
    {
        $id = (int) $id;
        return $this->builder->where('id', '=', $id);
    }

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
