<?php

namespace App\Services;

use App\Models\Plan;
use App\Repositories\PlanRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanService
{
    public function __construct(
        protected PlanRepository $repository
    ) {}

    public function list(): LengthAwarePaginator
    {
        return $this->repository->paginate();
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function show(Plan $plan): Plan
    {
        return $this->repository->findWithRelations($plan);
    }

    public function update(Plan $plan, array $data): ?Plan
    {
        return $this->repository->update($plan, $data);
    }

    public function delete(Plan $plan): ?bool
    {
        return $this->repository->delete($plan);
    }

    public function listAvailable()
    {
        return $this->repository->paginateAvailable();
    }
}
