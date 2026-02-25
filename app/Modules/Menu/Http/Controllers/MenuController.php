<?php

namespace App\Modules\Menu\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Menu\Http\Resources\MenuResource;
use App\Modules\Menu\Services\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(
        protected MenuService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $menus = $this->service->getMenusForUser($request->user());
        return response()->json(MenuResource::collection($menus));
    }
}
