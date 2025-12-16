<?php

namespace App\Repositories;

use App\Models\ClassModel;
use Illuminate\Pagination\LengthAwarePaginator;

class ClassRepository
{
    public function paginateWithRelations(): LengthAwarePaginator
    {
        return ClassModel::with(ClassModel::DEFAULT_RELATIONS)
            ->orderBy('plan_id')
            ->orderBy('start_time')
            ->paginate();
    }

    public function create(array $data): ClassModel
    {
        return ClassModel::create($data);
    }

    public function findWithRelations(ClassModel $class): ClassModel
    {
        return $class->load(ClassModel::DEFAULT_RELATIONS);
    }

    public function update(ClassModel $class, array $data): ClassModel
    {
        $class->update($data);
        return $class->fresh()->load(ClassModel::DEFAULT_RELATIONS);
    }

    public function delete(ClassModel $class): bool
    {
        return $class->delete();
    }

    public function getAvailableByPlan(int $planId)
    {
        return ClassModel::query()
            ->where('plan_id', $planId)
            ->available()
            ->get();
    }
}
