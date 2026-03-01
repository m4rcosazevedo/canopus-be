<?php

namespace App\Modules\Area\Repositories;

use App\Builders\QueryFilter;
use App\Modules\Area\Models\Area;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AreaRepository
{
    public function __construct(
        protected Area $model
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

    public function findById(int $id): ?Area
    {
        return $this->model->find($id);
    }

    public function create(array $data): Area
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Area
    {
        $area = $this->findById($id);

        if (!$area) {
            return null;
        }

        $area->update($data);

        return $area->refresh();
    }

    public function delete(int $id): bool
    {
        $area = $this->findById($id);

        return (bool)$area?->delete();
    }
}
