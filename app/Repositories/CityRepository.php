<?php

namespace App\Repositories;

use App\Models\City;
use App\Filters\CityFilter;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CityRepository
{
    private const DEFAULT_RELATIONS = ['state'];

    public function paginate(Request $request): LengthAwarePaginator
    {
        return City::query()
            ->with(self::DEFAULT_RELATIONS)
            ->filter(new CityFilter($request))
            ->orderBy('name')
            ->paginate();
    }

    public function find(City $city): City
    {
        return $city->load(self::DEFAULT_RELATIONS);
    }

    public function findByName(string $name, string $abbr): City|null
    {
        return City::query()
            ->with(self::DEFAULT_RELATIONS)
            ->where('name', $name)
            ->whereHas('state', function ($query) use ($abbr) {
                $query->where('abbr', $abbr);
            })->first();
    }

    public function create(array $data): City
    {
        return City::create($data)->load(self::DEFAULT_RELATIONS);
    }

    public function update(City $city, array $data): City
    {
        $city->update($data);

        return $city->fresh()->load(self::DEFAULT_RELATIONS);
    }

    public function delete(City $city): bool
    {
        return $city->delete();
    }
}
