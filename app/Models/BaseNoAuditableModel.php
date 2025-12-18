<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BaseNoAuditableModel extends Model
{
    use Filterable;

    public function newEloquentBuilder($query)
    {
        return new \App\Builders\CustomBuilder($query);
    }

    public function getUpdatedAtAttribute($value): string
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
