<?php

namespace App\Modules\State\Repositories;

use App\Modules\State\Filters\StateFilter;
use App\Modules\State\Model\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


class StateRepository
{
    public function all(Request $request): Collection
    {
        return $this->baseQuery($request)->get();
    }

    public function paginate(Request $request): LengthAwarePaginator
    {
        return $this->baseQuery($request)->paginate();
    }

    public function create(array $data): State
    {
        return State::create($data);
    }

    public function update(State $state, array $data): State
    {
        $state->update($data);

        return $state->fresh();
    }

    public function delete(State $state): bool
    {
        return $state->delete();
    }

    private function baseQuery(Request $request): Builder
    {
        return State::query()
            ->orderBy('abbr')
            ->filter(new StateFilter($request));
    }
}
