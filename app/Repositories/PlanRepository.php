<?php

namespace App\Repositories;

use App\Models\Plan;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanRepository
{
    public function paginate(): LengthAwarePaginator
    {
        return Plan::with(Plan::DEFAULT_RELATIONS)
            ->paginate();
    }

    public function create(array $data)
    {
        return Plan::create($data);
    }

    public function findWithRelations(Plan $plan): Plan
    {
        return $plan->load(Plan::DEFAULT_RELATIONS);
    }

    public function update(Plan $plan, array $data): ?Plan
    {
        $plan->update($data);
        return $plan->fresh();
    }

    public function delete(Plan $plan): ?bool
    {
        return $plan->delete();
    }

    public function paginateAvailable()
    {
        return Plan::available()->paginate();
    }
}
