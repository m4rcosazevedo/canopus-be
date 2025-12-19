<?php

namespace App\Http\Controllers;

use App\Http\Requests\City\CityRequest;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Repositories\CityRepository;
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
        $cities = $this->repository->paginate($request);
        return CityResource::collection($cities);
    }

    public function store(CityRequest $request): CityResource
    {
        $city = $this->repository->create($request->validated());
        return new CityResource($city);
    }

    public function show(City $city): CityResource
    {
        $city = $this->repository->find($city);
        return new CityResource($city);
    }

    public function update(CityRequest $request, City $city): CityResource
    {
        $city = $this->repository->update($city, $request->validated());
        return new CityResource($city);
    }

    public function destroy(City $city): Response
    {
        $this->repository->delete($city);
        return response()->noContent();
    }
}
