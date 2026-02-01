<?php

namespace App\Modules\UserType\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\UserType\Http\Requests\UserTypeRequest;
use App\Modules\UserType\Http\Resources\UserTypeResource;
use App\Modules\UserType\Model\UserType;
use App\Modules\UserType\Repositories\UserTypeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UserTypeController extends Controller
{
    public function __construct(
        protected readonly UserTypeRepository $repository,
    )
    {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return UserTypeResource::collection(
            $this->repository->paginate($request)
        );
    }

    public function show(UserType $userType): UserTypeResource
    {
        return new UserTypeResource(
            $this->repository->find($userType)
        );
    }

    public function store(UserTypeRequest $request): UserTypeResource
    {
        return  new UserTypeResource(
            $this->repository->create($request->validated())
        );
    }

    public function update(UserTypeRequest $request, UserType $userType): UserTypeResource
    {
        return  new UserTypeResource(
            $this->repository->update($userType, $request->validated())
        );
    }

    public function destroy(UserType $userType): Response
    {
        $this->repository->delete($userType);

        return response()->noContent();
    }

    public function options(Request $request): JsonResponse
    {
        return response()->json([
            "data" => $this->repository->options($request)
        ]);
    }
}
