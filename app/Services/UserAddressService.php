<?php

namespace App\Services;

use DomainException;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Repositories\AddressRepository;
use App\Repositories\UserAddressRepository;

class UserAddressService
{
    private const MAX_ADDRESSES_PER_USER = 10;

    public function __construct(
        protected AddressRepository $addressRepository,
        protected UserAddressRepository $repository,
    ) {}

    public function index(User $user): LengthAwarePaginator
    {
        return $this->repository->paginate($user);
    }

    public function show(User $user, UserAddress $userAddress): UserAddress
    {
        $this->abortIfUserIsDifferent($user, $userAddress);
        return  $this->repository->find($userAddress);
    }

    public function create(User $user, array $data): UserAddress
    {
        return DB::transaction(function () use ($user, $data) {
            $count = $this->repository->countByUser($user->id);

            if ($count >= self::MAX_ADDRESSES_PER_USER) {
                throw new DomainException(
                    sprintf(
                        'O cliente já possui o número máximo de %d endereços cadastrados.',
                        self::MAX_ADDRESSES_PER_USER
                    )
                );
            }

            $address = $this->addressRepository->findOrCreate([
                'zip_code'    => $data['zip_code'],
                'street_type' => trim($data['street_type']),
                'street_name' => trim($data['street_name']),
                'district'    => trim($data['district']) ?? null,
                'city_id'     => $data['city_id'],
            ]);

            if ($this->repository->existsForUser($user->id, $address->id)) {
                throw new DomainException('Este endereço já está cadastrado para o usuário.');
            }

            if ($count === 0) {
                $data['is_default'] = true;
            }

            $this->clearDefaultForUser($data, $user->id);

            return $this->repository->create([
                'user_id'    => $user->id,
                'address_id' => $address->id,
                'number'     => $data['number'] ?? null,
                'complement' => $data['complement'] ?? null,
                'is_default' => $data['is_default'] ?? false,
            ]);
        });
    }

    public function update(array $data, User $user, UserAddress $userAddress): UserAddress
    {
        $this->abortIfUserIsDifferent($user, $userAddress);

        return DB::transaction(function () use ($data, $user, $userAddress) {
            $isChangingFromDefaultToFalse = ($userAddress->is_default && isset($data['is_default']) && $data['is_default'] == false);

            $this->clearDefaultForUser($data, $user->id);

            $updatedAddress = $this->repository->update($data, $userAddress);

            if ($isChangingFromDefaultToFalse) {
                $anotherAddress = $this->repository->getAnotherForUser($user->id, $updatedAddress->id);
                if ($anotherAddress) {
                    $this->repository->setAsDefault($anotherAddress);
                } else {
                    $this->repository->setAsDefault($updatedAddress);
                }
            }
            return $updatedAddress;
        });
    }

    public function delete(User $user, UserAddress $userAddress): void
    {
        $this->abortIfUserIsDifferent($user, $userAddress);

        DB::transaction(function () use ($userAddress) {

            $userId = $userAddress->user_id;
            $wasDefault = $userAddress->is_default;

            $this->repository->delete($userAddress);

            if ($wasDefault) {
                $anotherAddress = $this->repository
                    ->getAnotherForUser($userId, $userAddress->id);

                if ($anotherAddress) {
                    $this->repository->setAsDefault($anotherAddress);
                }
            }
        });
    }

    private function abortIfUserIsDifferent(User $user, UserAddress $address): void
    {
        abort_if($address->user_id !== $user->id, 404, 'Endereço não encontrado para este usuário.');
    }

    private function clearDefaultForUser(array $data, int $userId): void
    {
        if (!empty($data['is_default'])) {
            $this->repository->clearDefaultForUser($userId);
        }
    }
}
