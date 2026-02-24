<?php

namespace App\Modules\Permission\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Permission\Http\Requests\StorePermissionRequest;
use App\Modules\Permission\Http\Requests\UpdatePermissionRequest;
use App\Modules\Permission\Http\Resources\PermissionResource;
use App\Modules\Permission\Repositories\PermissionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PermissionController extends Controller
{
    public function __construct(
        private readonly PermissionRepository $repository
    )
    { }

    public function index(Request $request): AnonymousResourceCollection
    {
        return PermissionResource::collection(
            $this->repository->paginate($request)
        );
    }

    public function show(int $id): PermissionResource|JsonResponse
    {
        return new PermissionResource(
            $this->repository->find($id)
        );
    }

    public function store(StorePermissionRequest $request): PermissionResource
    {
        return new PermissionResource(
            $this->repository->create($request->validated())
        );
    }

    public function update(UpdatePermissionRequest $request, int $id): PermissionResource|JsonResponse
    {
        $permission = $this->repository->find($id);

        $this->repository->update($id, $request->validated());

        return new PermissionResource($permission->refresh());
    }

    public function destroy(int $id): Response
    {
        $this->repository->delete($id);

        return response()->noContent();
    }
}
