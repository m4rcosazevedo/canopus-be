<?php

namespace App\Filters;

use App\Builders\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class AddressFilter extends QueryFilter
{
    public function streetType(string $streetType)
    {
        return $this->builder->where('street_type', 'LIKE', "%$streetType%");
    }

    public function streetName(string $streetName)
    {
        return $this->builder->where('street_name', 'LIKE', "%$streetName%");
    }

    public function district(string $district)
    {
        return $this->builder->where('district', 'LIKE', "%$district%");
    }

    public function zipCode(string $zipCode)
    {
        $zipCode = preg_replace('/[^0-9]/', '', $zipCode);
        return $this->builder->where('zip_code', '=', $zipCode);
    }

    public function cityId($cityId)
    {
        $cityId = (int) $cityId;
        return $this->builder->where('city_id', '=', $cityId);
    }

    public function city(string $city): Builder
    {
        return $this->builder->whereHas('city', function ($query) use ($city) {
            $query->where('name', 'LIKE', "%$city%");
        });
    }
}
