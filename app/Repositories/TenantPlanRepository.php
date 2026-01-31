<?php

namespace App\Repositories;

use App\Models\TenantPlan;
use Illuminate\Pagination\LengthAwarePaginator;

class TenantPlanRepository
{
    public function paginate(): LengthAwarePaginator
    {
        return TenantPlan::query()
            ->paginate();
    }

    public function paginatePublic(): LengthAwarePaginator
    {
        return TenantPlan::query()
            ->active()
            ->paginate();
    }

    public function create(array $data): TenantPlan
    {
        return TenantPlan::create($data);
    }

    public function update(TenantPlan $model, array $data): ?TenantPlan
    {
        $model->update($data);

        return $model->refresh();
    }

    public function delete(TenantPlan $model): ?TenantPlan
    {
        $model->update(['is_active' => false]);

        return $model;
    }
}
