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

    public function getAnotherForUser(int $userId, int $ignoreId): ?UserDocument
    {
        return UserDocument::where('user_id', $userId)
            ->where('id', '!=', $ignoreId)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->first();
    }

    public function setAsDefault(UserDocument $userDocument): void
    {
        $userDocument->update(['is_default' => true]);
    }

    public function clearDefaultForUser(int $userId): void
    {
        UserDocument::where('user_id', $userId)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }

    public function countByUser(int $userId): int
    {
        return UserDocument::where('user_id', $userId)->count();
    }
}
