<?php

namespace App\Services;

use App\Models\ClassModel;
use App\Repositories\ClassRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ClassService
{
    public function __construct(
        protected ClassRepository $repository
    ) {}

    public function list(): LengthAwarePaginator
    {
        return $this->repository->paginateWithRelations();
    }

    public function create(array $data): ClassModel
    {
        $class = $this->repository->create($data);
        return $this->repository->findWithRelations($class);
    }

    public function show(ClassModel $class): ClassModel
    {
        return $this->repository->findWithRelations($class);
    }

    public function update(ClassModel $class, array $data): ClassModel
    {
        return $this->repository->update($class, $data);
    }

    public function delete(ClassModel $class): bool
    {
        return $this->repository->delete($class);
    }

    public function availableByPlan(int $planId)
    {
        return $this->repository->getAvailableByPlan($planId);
    }
}
