<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Pagination\LengthAwarePaginator;

class UserAddressRepository
{
    public function paginate(User $user): LengthAwarePaginator
    {
        return $user->addresses()
            ->with(UserAddress::DEFAULT_RELATIONS)
            ->paginate();
    }

    public function find(UserAddress $address): UserAddress
    {
        return $address->load(UserAddress::DEFAULT_RELATIONS);
    }

    public function create(array $data): UserAddress
    {
        return UserAddress::create($data);
    }

    public function update(array $data, UserAddress $address): UserAddress
    {
        $address->update($data);
        return $address->refresh()->load(UserAddress::DEFAULT_RELATIONS);
    }

    public function delete(UserAddress $address): bool
    {
        return (bool) $address->delete();
    }

    public function getAnotherForUser(int $userId, int $ignoreId): ?UserAddress
    {
        return UserAddress::where('user_id', $userId)
            ->where('id', '!=', $ignoreId)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->first();
    }

    public function setAsDefault(UserAddress $userAddress): void
    {
        $userAddress->update(['is_default' => true]);
    }

    public function existsForUser(int $userId, int $addressId): bool
    {
        return UserAddress::where('user_id', $userId)
            ->where('address_id', $addressId)
            ->exists();
    }

    public function clearDefaultForUser(int $userId): void
    {
        UserAddress::where('user_id', $userId)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }

    public function countByUser(int $userId): int
    {
        return UserAddress::where('user_id', $userId)->count();
    }
}
