<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserDocument\StoreUserDocumentRequest;
use App\Http\Requests\UserDocument\UpdateUserDocumentRequest;
use App\Http\Resources\UserDocumentResource;
use App\Models\User;
use App\Models\UserDocument;
use App\Services\UserDocumentService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UserDocumentController extends Controller
{
    public function __construct(
        protected readonly UserDocumentService $service
    ) { }

    public function index(User $user): AnonymousResourceCollection
    {
        return UserDocumentResource::collection(
            $this->service->index($user)
        );
    }

    public function show(User $user, UserDocument $document): UserDocumentResource
    {
        return new UserDocumentResource(
            $this->service->show($user, $document)
        );
    }

    public function store(StoreUserDocumentRequest $request, User $user): UserDocumentResource
    {
        return new UserDocumentResource(
            $this->service->create($user, $request->validated())
        );
    }

    public function update(UpdateUserDocumentRequest $request, User $user, UserDocument $document): UserDocumentResource
    {
        return new UserDocumentResource(
            $this->service->update($request->validated(), $user, $document)
        );
    }

    public function destroy(User $user, UserDocument $document): Response
    {
        $this->service->delete($user, $document);
        return response()->noContent();
    }
}
