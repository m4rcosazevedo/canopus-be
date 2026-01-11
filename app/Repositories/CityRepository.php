<?php

namespace App\Repositories;

use App\Models\City;
use App\Filters\CityFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class CityRepository
{
    private const DEFAULT_RELATIONS = ['state'];

    public function all(Request $request): Collection
    {
        return $this->baseQuery($request)->get();
    }

    public function paginate(Request $request): LengthAwarePaginator
    {
        return $this->baseQuery($request)
            ->with(self::DEFAULT_RELATIONS)
            ->paginate();
    }

    public function find(City $city): City
    {
        return $city->load(self::DEFAULT_RELATIONS);
    }

    public function findByName(string $name, string $stateAbbr): City|null
    {
        return City::query()
            ->with(self::DEFAULT_RELATIONS)
            ->where('name', $name)
            ->whereHas('state', fn (Builder $query) =>
                $query->where('abbr', $stateAbbr)
            )
            ->first();
    }

    public function create(array $data): City
    {
        return City::create($data)->load(self::DEFAULT_RELATIONS);
    }

    public function update(City $city, array $data): City
    {
        $city->update($data);

        return $city->refresh()->load(self::DEFAULT_RELATIONS);
    }

    public function delete(City $city): bool
    {
        return (bool) $city->delete();
    }

    private function baseQuery(Request $request): Builder
    {
        return City::query()
            ->filter(new CityFilter($request))
            ->orderBy('name');
    }
}
