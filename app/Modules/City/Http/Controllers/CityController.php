<?php

namespace App\Modules\City\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\City\Http\Requests\CityRequest;
use App\Modules\City\Http\Resources\CityResource;
use App\Modules\City\Models\City;
use App\Modules\City\Repositories\CityRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CityController extends Controller
{
    public function __construct(
        private readonly CityRepository $repository
    ) { }

    public function index(Request $request): AnonymousResourceCollection
    {
        return CityResource::collection(
            $this->repository->paginate($request)
        );
    }

    public function store(CityRequest $request): CityResource
    {
        return new CityResource(
            $this->repository->create($request->validated())
        );
    }

    public function show(City $city): CityResource
    {
        return new CityResource(
            $this->repository->find($city)
        );
    }

    public function update(CityRequest $request, City $city): CityResource
    {
        return new CityResource(
            $this->repository->update($city, $request->validated())
        );
    }

    public function destroy(City $city): Response
    {
        $this->repository->delete($city);

        return response()->noContent();
    }

    public function options(Request $request): JsonResponse
    {
        $request->validate(['stateId' => 'required']);

        return response()->json([
            'data' => $this->repository
                ->all($request)
                ->map(fn (City $city) => [
                    'label' => $city->name,
                    'value' => $city->id,
                ]),
        ]);
    }
}
