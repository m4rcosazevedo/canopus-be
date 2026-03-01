<?php

namespace App\Modules\Plan\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Plan\Filters\PlanFilter;
use App\Modules\Plan\Http\Requests\PlanRequest;
use App\Modules\Plan\Http\Resources\PlanResource;
use App\Modules\Plan\Repositories\PlanRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PlanController extends Controller
{
    public function __construct(
        protected PlanRepository $repository
    ) {}

    /**
     * GET /plans
     */
    public function index(PlanFilter $filter): AnonymousResourceCollection
    {
        return PlanResource::collection(
            $this->repository->paginate($filter)
        );
    }

    /**
     * GET /plans/{id}
     */
    public function show(int $id): PlanResource
    {
        return new PlanResource(
            $this->repository->findById($id)
        );
    }

    /**
     * POST /plans
     */
    public function store(PlanRequest $request): PlanResource
    {
        return new PlanResource(
            $this->repository->create(
                $request->validated()
            )
        );
    }

    /**
     * PUT/PATCH /plans/{id}
     */
    public function update(PlanRequest $request, int $id): PlanResource
    {
        return new PlanResource(
            $this->repository->update(
                $id,
                $request->validated()
            )
        );
    }

    /**
     * DELETE /plans/{id}
     */
    public function destroy(int $id): Response
    {
        $this->repository->delete($id);

        return response()->noContent();
    }

}
