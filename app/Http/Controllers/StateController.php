<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
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
        return StateResource::collection(
            $this->repository->paginate($request)
        );
    }

    public function show(State $state): StateResource
    {
        return new StateResource($state);
    }

    public function store(StateRequest $request): StateResource
    {
        return new StateResource(
            $this->repository->create($request->validated())
        );
    }

    public function update(StateRequest $request, State $state): StateResource
    {
        return new StateResource(
            $this->repository->update($state, $request->validated())
        );
    }

    public function destroy(State $state): Response
    {
        $this->repository->delete($state);

        return response()->noContent();
    }

    public function options(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->repository
                ->all($request)
                ->map(fn (State $state) => [
                    'label' => $state->name,
                    'value' => $state->id,
                ]),
        ]);
    }

}
