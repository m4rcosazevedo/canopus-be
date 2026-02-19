<?php

namespace App\Modules\State\Filters;

use App\Builders\SortableQueryFilter;

class StateFilter extends SortableQueryFilter
{
    protected array $sortable = [
        'id'        => 'id',
        'name'      => 'name',
        'abbr'      => 'abbr',
        'ibgeCode'  => 'ibge_code',
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
