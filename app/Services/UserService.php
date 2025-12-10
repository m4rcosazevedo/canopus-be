<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        protected UserRepository $repository
    ) {}

    public function list(): LengthAwarePaginator
    {
        return $this->repository->paginate();
    }

    public function show(string|int $id): User
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return $this->repository->create($data);
    }

    public function update(string|int $id, array $data): User
    {
        $user = $this->repository->findById($id);

//        if (isset($data['password'])) {
//            $data['password'] = Hash::make($data['password']);
//        }

        return $this->repository->update($user, $data);
    }

    public function delete(string|int $id): bool
    {
        $user = $this->repository->findById($id);
        return $this->repository->delete($user);
    }
}
