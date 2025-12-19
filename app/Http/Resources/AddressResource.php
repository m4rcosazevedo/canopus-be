<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'zipCode' => $this->resource->zip_code,
            'streetType' => $this->resource->street_type,
            'streetName' => $this->resource->street_name,
            'district' => $this->resource->district,
            'city' => new CityResource($this->whenLoaded('city')),
            'createdAt' => $this->resource->created_at,
            'updatedAt' => $this->resource->updated_at,
        ];
    }
}
