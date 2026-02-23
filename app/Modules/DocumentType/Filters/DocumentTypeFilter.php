<?php

namespace App\Modules\DocumentType\Filters;

use App\Builders\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class DocumentTypeFilter extends QueryFilter
{
    public function userId($id)
    {
        $id = (int) $id;

        return $this->builder->whereDoesntHave('userDocuments', function (Builder $query) use ($id) {
            $query->where('user_id', $id);
        });
    }
}
