<?php

namespace App\Repositories;

use App\Filters\AddressFilter;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AddressRepository
{
    private const DEFAULT_RELATIONS = ['city.state'];

    public function paginate(Request $request): LengthAwarePaginator
    {
        return Address::query()
            ->with(self::DEFAULT_RELATIONS)
            ->filter(new AddressFilter($request))
            ->paginate();
    }

    public function find(Address $address): Address
    {
        return $address->load(self::DEFAULT_RELATIONS);
    }

    public function findByZipCode(string $zipCode): ?Address
    {
        return Address::with(self::DEFAULT_RELATIONS)
            ->where('zip_code', $zipCode)
            ->first();
    }

    public function create(array $data): Address
    {
        return Address::create($data)->load(self::DEFAULT_RELATIONS);
    }

    public function update(Address $address, array $data): Address
    {
        $address->update($data);

        return $address->fresh()->load(self::DEFAULT_RELATIONS);
    }

    public function delete(Address $address): bool
    {
        return $address->delete();
    }

    public function findOrCreate(array $data): Address
    {
        return Address::query()
            ->where('zip_code', $data['zip_code'])
            ->where('street_name', $data['street_name'])
            ->where('street_type', $data['street_type'] ?? null)
            ->where('district', $data['district'] ?? null)
            ->where('city_id', $data['city_id'])
            ->first()
            ?? $this->create($data);
    }
}
