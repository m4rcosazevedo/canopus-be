<?php

namespace App\Modules\UserType\Repositories;

use App\Modules\UserType\Model\UserType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserTypeRepository
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        return $this->baseQuery($request)->paginate();
    }

    public function options(Request $request): Collection
    {
        $options = $this->baseQuery($request)
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

    private function baseQuery(Request $request): Builder
    {
        return UserType::query();
//            ->filter();
    }
}
