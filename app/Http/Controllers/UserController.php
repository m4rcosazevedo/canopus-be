<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        protected UserService $service
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $users = $this->service->list();
        return UserResource::collection($users);
    }

    public function show(string $id): UserResource
    {
        $user = $this->service->show($id);
        return new UserResource($user);
    }

    public function store(StoreUserRequest $request): UserResource
    {
        $user = $this->service->create($request->validated());
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, string $id): UserResource
    {
        $user = $this->service->update($id, $request->validated());
        return new UserResource($user);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(null, 204);
    }
}
