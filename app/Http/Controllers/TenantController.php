<?php

namespace App\Http\Controllers;


use App\Http\Requests\Tenant\TenantRequest;
use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use App\Modules\Tenant\Http\Requests\TenantRegisterRequest;
use App\Modules\UserType\Enums\UserTypeIdEnum;
use App\Repositories\TenantRepository;
use App\Services\UserDocumentService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    public function __construct (
        protected readonly TenantRepository $repository
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

    public function store(TenantRequest $request): TenantResource
    {
        return new TenantResource(
            $this->repository->create($request->validated())
        );
    }

    public function update(TenantRequest $request, Tenant $tenant): TenantResource
    {
        return new TenantResource(
            $this->repository->update($tenant, $request->validated())
        );
    }

//    public function destroy()
//    {
//
//    }

    public function register(
        TenantRegisterRequest $request,
        UserService $userService,
        UserDocumentService $userDocumentService
    ): JsonResponse
    {
        DB::transaction(function () use ($request, $userService, $userDocumentService) {

            $tenantData = [
                'name' => $request->input('companyName'),
                'domain' => $request->input('domain'),
            ];
            $tenant = $this->repository->create($tenantData);

            $password = Hash::make($request->input('password'));
            $user = $userService->create([
                ...$request->only(['name', 'email', 'cellphone']),
                'password' => $password,
                'user_type_id' => UserTypeIdEnum::ADMINISTRATOR,
                'tenant_id' => $tenant->id,
            ]);

            $document = $userDocumentService->create(
                $user,
                $request->only(['number', 'document_type_id'])
            );

            return [
                'tenant' => $tenant,
                'user' => $user,
                'document' => $document,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Empresa foi registrada com sucesso'
        ], 201);
    }

}
