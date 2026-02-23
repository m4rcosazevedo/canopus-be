<?php

namespace App\Repositories;

use App\Filters\UserFilter;
use App\Models\User;

class UserRepository
{
    public function paginate()
    {
        return User::with($this->defaultRelations())
            ->filter(new UserFilter(request()))
            ->paginate();
    }

    public function findById(string|int $id): User
    {
        return User::with($this->defaultRelations())->findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $this->withRelations($user->fresh());
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function withRelations(User $user): User
    {
        return $user->load($this->defaultRelations());
    }

    private function defaultRelations(): array
    {
        return User::DEFAULT_RELATIONS;
    }
}
