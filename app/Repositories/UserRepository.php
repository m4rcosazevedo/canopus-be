<?php

namespace App\Repositories;

use App\Filters\UserFilter;
use App\Models\User;

class UserRepository
{
    public function paginate()
    {
        return User::with(User::DEFAULT_RELATIONS)
            ->filter(new UserFilter(request()))
            ->orderByDesc('id')
            ->paginate();
    }

    public function findById(string|int $id): User
    {
        return User::with(User::DEFAULT_RELATIONS)->findOrFail($id);
    }

    public function create(array $data): User
    {
        $user = User::create($data);
        return $user->load(User::DEFAULT_RELATIONS);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh()->load(User::DEFAULT_RELATIONS);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
