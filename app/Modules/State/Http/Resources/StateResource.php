<?php

namespace App\Modules\State\Http\Resources;

use App\Http\Resources\CityResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'abbr' => $this->resource->abbr,
            'cities' => CityResource::collection($this->whenLoaded('cities')),
            'ibgeCode' => $this->resource->ibge_code,
            'createdAt' => $this->resource->created_at,
            'updatedAt' => $this->resource->updated_at,
        ];
    }
}
