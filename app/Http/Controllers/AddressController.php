<?php

namespace App\Http\Controllers;

use App\Http\Requests\Address\AddressWithAutoCompleteRequest;
use App\Models\Address;
use App\Repositories\CityRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\AddressResource;
use App\Repositories\AddressRepository;
use App\Http\Requests\Address\AddressRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    public function __construct(
        protected readonly AddressRepository $repository,
        protected readonly CityRepository $cityRepository,
    ) { }

    public function index(Request $request): AnonymousResourceCollection
    {
        $addresses = $this->repository->paginate($request);
        return AddressResource::collection($addresses);
    }

    public function store(AddressRequest $request): AddressResource
    {
        $address = $this->repository->create($request->validated());
        return new AddressResource($address);
    }

    public function show(Address $address): AddressResource
    {
        $address = $this->repository->find($address);
        return new AddressResource($address);
    }

    public function update(AddressRequest $request, Address $address): AddressResource
    {
        $address = $this->repository->update($address, $request->validated());
        return new AddressResource($address);
    }

    public function destroy(Address $address): Response
    {
        $this->repository->delete($address);
        return response()->noContent();
    }

    public function storeByZipCode (AddressWithAutoCompleteRequest $request): JsonResponse|AddressResource
    {
        $externalData = $request->auto_complete_data;

        if (!$externalData) {
            Log::warning("storeByZipCode: CEP não encontrado nos provedores.");
            return response()->json(['message' => 'CEP não encontrado nos provedores.'], 404);
        }

        $city = $this->cityRepository->findByName(
            $externalData['city'],
            $externalData['state']
        );

        if (!$city) {
            Log::warning("storeByZipCode: A Cidade $externalData[city] não foi encontrada no sistema.");
            return response()->json(['message' => "A Cidade $externalData[city] não foi encontrada no sistema."], 404);
        }

        $data = [
            'zip_code'    => $externalData['zip_code'],
            'street_type' => $externalData['street_type'],
            'street_name' => $externalData['street_name'],
            'district'    => $externalData['district'],
            'city_id'     => $city->id
        ];

        $address = $this->repository->create($data);
        return new AddressResource($address);
    }
}
