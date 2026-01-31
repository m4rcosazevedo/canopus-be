<?php

namespace App\Http\Controllers;


use App\Http\Requests\Tenant\TenantPlanRequest;
use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use App\Repositories\TenantRepository;

class TenantController extends Controller
{
    public function __construct (
       protected readonly TenantRepository $repository,
    ) {}

    public function index()
    {
        return TenantResource::collection(
            $this->repository->paginate()
        );
    }

    public function show(Tenant $tenant): TenantResource
    {
        return new TenantResource($tenant);
    }

    public function store(TenantPlanRequest $request): TenantResource
    {
        return new TenantResource(
            $this->repository->create($request->validated())
        );
    }

    public function update(TenantPlanRequest $request, Tenant $tenant): TenantResource
    {
        return new TenantResource(
            $this->repository->update($tenant, $request->validated())
        );
    }

//    public function destroy()
//    {
//
//    }
}
