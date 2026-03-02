<?php

namespace App\Modules\Subject\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Subject\Filters\SubjectFilter;
use App\Modules\Subject\Http\Requests\SubjectRequest;
use App\Modules\Subject\Http\Resources\SubjectResource;
use App\Modules\Subject\Repositories\SubjectRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SubjectController extends Controller
{
    public function __construct(
        protected SubjectRepository $repository
    ) {}

    /**
     * GET /Subjects
     */
    public function index(SubjectFilter $filter): AnonymousResourceCollection
    {
        return SubjectResource::collection(
            $this->repository->paginate($filter)
        );
    }

    /**
     * GET /Subjects/{id}
     */
    public function show(int $id): SubjectResource
    {
        return new SubjectResource(
            $this->repository->findById($id)
        );
    }

    /**
     * POST /Subjects
     */
    public function store(SubjectRequest $request): SubjectResource
    {
        return new SubjectResource(
            $this->repository->create(
                $request->validated()
            )
        );
    }

    /**
     * PUT/PATCH /Subjects/{id}
     */
    public function update(SubjectRequest $request, int $id): SubjectResource
    {
        return new SubjectResource(
            $this->repository->update(
                $id,
                $request->validated()
            )
        );
    }

    /**
     * DELETE /Subjects/{id}
     */
    public function destroy(int $id): Response
    {
        $this->repository->delete($id);

        return response()->noContent();
    }

}
