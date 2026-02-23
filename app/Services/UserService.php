<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(
        protected UserRepository $repository,
        protected UserDocumentService $documentService
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
        return DB::transaction(function () use ($data) {

            $user = $this->repository->create(
                $this->formatUserData($data)
            );

            $this->documentService->create(
                $user,
                $this->formatDocumentData($data)
            );

            return $this->repository->withRelations($user);
        });
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

    private function formatUserData(array $data): array
    {
        return [
            'name'         => $data['name'],
            'email'        => $data['email'],
            'cellphone'    => $data['cellphone'],
            'user_type_id' => $data['user_type_id'],
            'password'     => $data['password'] ?? str()->random(16),
        ];
    }

    private function formatDocumentData(array $data): array
    {
        return [
            'document_type_id' => $data['document_type_id'],
            'number'           => $data['number'],
            'issuer'           => $data['issuer'] ?? null,
            'state_id'         => $data['state_id'] ?? null,
            'issued_at'        => $data['issued_at'] ?? null,
        ];
    }
}
