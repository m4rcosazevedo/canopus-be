<?php

namespace App\Http\Resources;

use App\Modules\State\Http\Resources\StateResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'ibgeCode' => $this->resource->ibge_code,
            'state' => new StateResource($this->whenLoaded('state')),
            'createdAt' => $this->resource->created_at,
            'updatedAt' => $this->resource->updated_at,
        ];
    }
}
