<?php

namespace App\Modules\Report\Filters;

use App\Builders\SortableQueryFilter;

class ReportFilter extends SortableQueryFilter
{
    protected array $sortable = [
        'id'        => 'id',
        'name'      => 'parameters->title',
        'createdAt' => 'created_at',
    ];

    public function id($id)
    {
        $id = (int) $id;
        return $this->builder->where('id', '=', $id);
    }

    public function name(string $name)
    {
        return $this->builder->where('parameters->title', 'LIKE', '%' . $name . '%');
    }

    public function status(string $status)
    {
        return $this->builder->where('status', '=', $status);
    }
}
