<?php

namespace App\Modules\UserType\Repositories;

use App\Builders\QueryFilter;
use App\Modules\UserType\Model\UserType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserTypeRepository
{
    public function paginate(QueryFilter $filters): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->filter($filters)
            ->paginate();
    }

    public function options(): Collection
    {
        $options = $this->baseQuery()
            ->orderByDescription()
            ->visible()
            ->get();

        return $options->map(fn($item) => [
            'value' => $item->id,
            'label' => $item->description,
        ]);
    }

    public function find(UserType $userType): UserType
    {
        return $userType;
    }

    public function create(array $data): UserType
    {
        return UserType::create($data);
    }

    public function update(UserType $userType, array $data): UserType
    {
        $userType->update($data);

        return $userType->refresh();
    }

    public function delete(UserType $userType): bool
    {
        return (bool) $userType->delete();
    }

    public function syncPermissions(UserType $userType, array $permissions): void
    {
        $userType->permissions()->sync($permissions);
    }

    public function showPermissions(UserType $userType): UserType
    {
        return $userType->load(['permissions']);
    }

    private function baseQuery(): Builder
    {
        return UserType::query();
    }
}
