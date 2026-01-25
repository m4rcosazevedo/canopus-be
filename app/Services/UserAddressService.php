<?php

namespace App\Services;

use DomainException;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use App\Repositories\AddressRepository;
use App\Repositories\UserAddressRepository;

class UserAddressService extends BaseUserResourceService
{
    private const MAX_ADDRESSES_PER_USER = 10;

    public function __construct(
        protected AddressRepository $addressRepository,
        UserAddressRepository $repository,
    ) {
        $this->repository = $repository;
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

    protected function getNotFoundMessage(): string
    {
        return 'Endereço não encontrado para este usuário.';
    }
}
