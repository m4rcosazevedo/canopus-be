<?php

namespace App\Http\Controllers;

use App\Http\Requests\TenantPlan\TenantPlanRequest;
use App\Http\Resources\TenantPlanPublicResource;
use App\Models\TenantPlan;
use App\Repositories\TenantPlanRepository;
use App\Http\Resources\TenantPlanResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TenantPlanController extends Controller
{
    public function __construct (
       protected readonly TenantPlanRepository $repository,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return TenantPlanResource::collection(
            $this->repository->paginate()
        );
    }

    public function list(): AnonymousResourceCollection
    {
        return TenantPlanPublicResource::collection(
            $this->repository->paginatePublic()
        );
    }

    public function show(TenantPlan $tenantPlan): TenantPlanResource
    {
        return new TenantPlanResource($tenantPlan);
    }

    public function store(TenantPlanRequest $request): TenantPlanResource
    {
        return new TenantPlanResource(
            $this->repository->create($request->validated())
        );
    }

    public function update(TenantPlanRequest $request, TenantPlan $tenantPlan): TenantPlanResource
    {
        return new TenantPlanResource(
            $this->repository->update($tenantPlan, $request->validated())
        );
    }

    public function destroy(TenantPlan $tenantPlan): Response
    {
        $this->repository->delete($tenantPlan);
        return response()->noContent();
    }
}
