<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserDocument\StoreUserDocumentRequest;
use App\Http\Requests\UserDocument\UpdateUserDocumentRequest;
use App\Http\Resources\UserDocumentResource;
use App\Models\User;
use App\Models\UserDocument;
use App\Repositories\UserDocumentRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UserDocumentController extends Controller
{
    public function __construct(
        protected readonly UserDocumentRepository $repository
    ) { }

    public function index(User $user): AnonymousResourceCollection
    {
        $documents = $this->repository->paginate($user);
        return UserDocumentResource::collection($documents);
    }

    public function show(User $user, UserDocument $document): UserDocumentResource
    {
        $document = $this->repository->find($document);
        return new UserDocumentResource($document);
    }

    public function store(StoreUserDocumentRequest $request, User $user): UserDocumentResource
    {
        $document = $this->repository->create($request->validated(), $user);
        return new UserDocumentResource($document);
    }

    public function update(UpdateUserDocumentRequest $request, User $user, UserDocument $document): UserDocumentResource
    {
        abort_if($document->user_id !== $user->id, 404, 'Documento não encontrado para este usuário.');

        $updated = $this->repository->update($request->validated(), $document);
        return new UserDocumentResource($updated);
    }

    public function destroy(User $user, UserDocument $document): Response
    {
        abort_if($document->user_id !== $user->id, 404, 'Documento não encontrado para este usuário.');

        $this->repository->delete($document);
        return response()->noContent();
    }
}
