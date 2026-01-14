<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Pagination\LengthAwarePaginator;

class UserDocumentRepository
{
    public function paginate(User $user): LengthAwarePaginator
    {
        return $user->documents()
            ->with(UserDocument::DEFAULT_RELATIONS)
            ->paginate();
    }

    public function find(UserDocument $document): UserDocument
    {
        return $document->load(UserDocument::DEFAULT_RELATIONS);
    }

    public function create(array $data, User $user): UserDocument
    {
        $document = $user->documents()->create($data);
        return $document->load(UserDocument::DEFAULT_RELATIONS);
    }

    public function update(array $data, UserDocument $userDocument): UserDocument
    {
        $userDocument->update($data);
        return $userDocument->fresh()->load(UserDocument::DEFAULT_RELATIONS);
    }

    public function delete(UserDocument $userDocument): bool
    {
        return $userDocument->delete();
    }
}
