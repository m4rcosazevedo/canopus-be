<?php

namespace App\Repositories;

use App\Filters\StateFilter;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class StateRepository
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        return State::orderBy('abbr')
            ->filter(new StateFilter($request))
            ->paginate();
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
}
