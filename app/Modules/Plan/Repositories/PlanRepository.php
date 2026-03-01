<?php

namespace App\Modules\Plan\Repositories;

use App\Builders\QueryFilter;
use App\Modules\Plan\Models\Plan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanRepository
{
    public function __construct(
        protected Plan $model
    ) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(QueryFilter $filters): LengthAwarePaginator
    {
        return $this->model
            ->filter($filters)
            ->paginate();
    }

    public function findById(int $id): ?Plan
    {
        return $this->model->find($id);
    }

    public function create(array $data): Plan
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Plan
    {
        $plan = $this->findById($id);

        if (!$plan) {
            return null;
        }

        $plan->update($data);

        return $plan->refresh();
    }

    public function delete(int $id): bool
    {
        $plan = $this->findById($id);

        return (bool)$plan?->delete();
    }
}
