<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\StateResource;
use App\Repositories\StateRepository;
use App\Http\Requests\State\StateRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StateController extends Controller
{
    public function __construct(
        private readonly StateRepository $repository
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $states = $this->repository->paginate($request);
        return StateResource::collection($states);
    }

    public function show(State $state): StateResource
    {
        return new StateResource($state);
    }

    public function store(StateRequest $request): StateResource
    {
        $state = $this->repository->create($request->validated());
        return new StateResource($state);
    }

    public function update(StateRequest $request, State $state)
    {
        $state = $this->repository->update($state, $request->validated());
        return new StateResource($state);
    }

    public function destroy(State $state): Response
    {
        $this->repository->delete($state);
        return response()->noContent();
    }
}
