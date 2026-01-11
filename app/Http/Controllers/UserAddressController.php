<?php

namespace App\Http\Controllers;

use DomainException;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\UserAddressService;
use App\Repositories\AddressRepository;
use App\Http\Resources\UserAddressResource;
use App\Repositories\UserAddressRepository;
use App\Http\Requests\UserAddress\UserAddressCreateRequest;
use App\Http\Requests\UserAddress\UserAddressUpdateRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserAddressController extends Controller
{
    public function __construct(
        protected AddressRepository $addressRepository,
        protected UserAddressRepository $repository,
        protected UserAddressService $service,
    ) {}

    public function index(User $user): AnonymousResourceCollection
    {
        return UserAddressResource::collection(
            $this->service->index($user)
        );
    }

    public function show(User $user, UserAddress $address): UserAddressResource
    {
        return new UserAddressResource(
            $this->service->show($user, $address)
        );
    }

    public function store(UserAddressCreateRequest $request, User $user): UserAddressResource|JsonResponse
    {
        try {
            $userAddress = $this->service->create($user, $request->validated());

            return new UserAddressResource(
                $userAddress->load(UserAddress::DEFAULT_RELATIONS)
            );
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function update(UserAddressUpdateRequest $request, User $user, UserAddress $address): UserAddressResource
    {
        return new UserAddressResource(
            $this->service->update($request->validated(), $user, $address)
        );
    }

    public function destroy(User $user, UserAddress $address): Response
    {
        $this->service->delete($user, $address);

        return response()->noContent();
    }


}
