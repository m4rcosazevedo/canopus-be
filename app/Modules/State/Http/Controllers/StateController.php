<?php

namespace App\Modules\State\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\State\Http\Requests\StateRequest;
use App\Modules\State\Http\Resources\StateResource;
use App\Modules\State\Model\State;
use App\Modules\State\Repositories\StateRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

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
