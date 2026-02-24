<?php

namespace App\Modules\DocumentType\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\DocumentType\Http\Requests\DocumentTypeRequest;
use App\Modules\DocumentType\Http\Resources\DocumentTypeResource;
use App\Modules\DocumentType\Models\DocumentType;
use App\Modules\DocumentType\Repositories\DocumentTypeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class DocumentTypeController extends Controller
{
    public function __construct(
        protected readonly DocumentTypeRepository $repository,
    )
    {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return DocumentTypeResource::collection(
            $this->repository->paginate($request)
        );
    }

    public function show(DocumentType $documentType): DocumentTypeResource
    {
        return new DocumentTypeResource(
            $this->repository->find($documentType)
        );
    }

    public function store(DocumentTypeRequest $request): DocumentTypeResource
    {
        return new DocumentTypeResource(
            $this->repository->create($request->validated())
        );
    }

    public function update(DocumentTypeRequest $request, DocumentType $documentType): DocumentTypeResource
    {
        return new DocumentTypeResource(
            $this->repository->update($documentType, $request->validated())
        );
    }

    public function destroy(DocumentType $documentType): Response
    {
        $this->repository->delete($documentType);

        return response()->noContent();
    }

    public function options(Request $request): JsonResponse
    {
        return response()->json([
            "data" => $this->repository->options($request)
        ]);
    }
}
