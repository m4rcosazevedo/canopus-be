<?php

namespace App\Modules\Menu\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Menu\Http\Requests\StoreMenuRequest;
use App\Modules\Menu\Http\Requests\UpdateMenuRequest;
use App\Modules\Menu\Http\Resources\MenuResource;
use App\Modules\Menu\Repositories\MenuRepository;
use App\Modules\Menu\Services\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MenuController extends Controller
{
    public function __construct(
        protected MenuService $service,
        protected MenuRepository $repository
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return MenuResource::collection(
            $this->repository->paginate()
        );
    }

    public function store(StoreMenuRequest $request): MenuResource
    {
        return new MenuResource(
            $this->repository->create($request->validated())
        );
    }

    public function show(int $id): MenuResource
    {
        return new MenuResource(
            $this->repository->findById($id)
        );
    }

    public function update(UpdateMenuRequest $request, int $id): MenuResource
    {
        return new MenuResource(
            $this->repository->update($id, $request->validated())
        );
    }

    public function options(): JsonResponse
    {
        return response()->json([
            "data" => $this->repository->all()->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->name,
            ])
        ]);
    }

    public function destroy(int $id): Response
    {
        $this->repository->delete($id);
        return response()->noContent();
    }

    public function available(Request $request): AnonymousResourceCollection
    {
        return MenuResource::collection(
            $this->service->getMenusForUser($request->user())
        );
    }
}
