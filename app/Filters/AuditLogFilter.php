<?php

namespace App\Filters;

use App\Builders\QueryFilter;
use Carbon\Carbon;

class AuditLogFilter extends QueryFilter
{
    public function event(string $event)
    {
        return $this->builder->where('event', '=', $event);
    }

    public function email(string $email)
    {
        $value = addcslashes(trim($email), '%_');

        return $this->builder->where('user_email', '=', $value);
    }

    public function startAt(string $startAt)
    {
        $start = Carbon::parse($startAt)->startOfDay();
        $end = Carbon::parse(request()->endAt)->endOfDay();

        return $this->builder->whereBetween('created_at', [$start, $end]);
    }

    public function model(string $model)
    {
        $modelName = "App\\Models\\" . ucfirst($model);
        return $this->builder->where('auditable_type', $modelName);
    }

    public function modelId(string $modelId)
    {
        $modelId = (int) $modelId;
        return $this->builder->where('auditable_id', $modelId);
    }
}
