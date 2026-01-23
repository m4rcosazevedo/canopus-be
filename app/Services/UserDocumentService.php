<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDocument;
use App\Repositories\UserDocumentRepository;
use Illuminate\Support\Facades\DB;

class UserDocumentService extends BaseUserResourceService
{
    public function __construct(UserDocumentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(User $user, array $data): UserDocument
    {
        return DB::transaction(function () use ($user, $data) {
            $count = $this->repository->countByUser($user->id);

            if ($count === 0) {
                $data['is_default'] = true;
            }

            $this->clearDefaultForUser($data, $user->id);

            return $this->repository->create($data, $user);
        });
    }

    protected function getNotFoundMessage(): string
    {
        return 'Documento não encontrado para este usuário.';
    }
}
