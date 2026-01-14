<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserTypeResource;
use App\Repositories\UserTypeRepository;
use App\Http\Requests\UserType\UserTypeRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
