<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'userId'        => $this->user_id,
            'number'        => $this->number,
            'complement'    => $this->complement,
            'isDefault'     => (bool) $this->is_default,
            'address'       => new AddressResource($this->whenLoaded('address')),
            'createdAt'     => $this->resource->created_at,
            'updatedAt'     => $this->resource->updated_at,
        ];
    }
}
