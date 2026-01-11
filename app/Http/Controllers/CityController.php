<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CityResource;
use App\Repositories\CityRepository;
use App\Http\Requests\City\CityRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
