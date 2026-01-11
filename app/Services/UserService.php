<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $data['password'] =  $data['password'] ?? str()->random(16);
        return $this->repository->create($data);
    }

    public function update(string|int $id, array $data): User
    {
        $user = $this->repository->findById($id);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $this->repository->update($user, $data);
    }

    public function delete(string|int $id): bool
    {
        if (auth()->id() == $id) {
            throw new \Exception("Você não pode excluir sua própria conta.");
        }

        $user = $this->repository->findById($id);
        return $this->repository->delete($user);
    }
}
