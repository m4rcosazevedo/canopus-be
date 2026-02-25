<?php

namespace App\Modules\Permission\Repositories;

use App\Modules\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

readonly class PermissionRepository
{
    public function __construct(private Permission $model)
    {
    }

    public function paginate(Request $request): LengthAwarePaginator
    {
        return $this->model->paginate();
    }

    public function all(): Collection
    {
        return $this->model
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): ?Permission
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Permission
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->find($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->find($id)->delete();
    }
}
