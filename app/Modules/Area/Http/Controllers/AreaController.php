<?php

namespace App\Modules\Area\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Area\Filters\AreaFilter;
use App\Modules\Area\Http\Requests\AreaRequest;
use App\Modules\Area\Http\Resources\AreaResource;
use App\Modules\Area\Repositories\AreaRepository;
use App\Modules\Area\Models\Area;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AreaController extends Controller
{
    public function __construct(
        protected AreaRepository $repository
    ) {}

    /**
     * GET /Areas
     */
    public function index(AreaFilter $filter): AnonymousResourceCollection
    {
        return AreaResource::collection(
            $this->repository->paginate($filter)
        );
    }

    /**
     * GET /Areas/{id}
     */
    public function show(int $id): AreaResource
    {
        return new AreaResource(
            $this->repository->findById($id)
        );
    }

    /**
     * POST /Areas
     */
    public function store(AreaRequest $request): AreaResource
    {
        return new AreaResource(
            $this->repository->create(
                $request->validated()
            )
        );
    }

    /**
     * PUT/PATCH /Areas/{id}
     */
    public function update(AreaRequest $request, int $id): AreaResource
    {
        return new AreaResource(
            $this->repository->update(
                $id,
                $request->validated()
            )
        );
    }

    /**
     * DELETE /Areas/{id}
     */
    public function destroy(int $id): Response
    {
        $this->repository->delete($id);

        return response()->noContent();
    }

    /**
     * GET /Areas/Options
     */
    public function options ()
    {
        $options = $this->repository->all();
        return response()->json([
            'data' => $options->map(fn (Area $city) => [
                'label' => $city->name,
                'value' => $city->id,
            ])
        ]);
    }
}
