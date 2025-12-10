<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class CustomBuilder extends Builder
{
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $total = null): LengthAwarePaginator
    {
        $perPage ??= min((int) Request::input('limit', 10), 100);

        return parent::paginate($perPage, $columns, $pageName, $page);
    }
}
