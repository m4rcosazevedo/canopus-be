<?php

namespace App\Http\Resources;

use App\Modules\Tenant\Http\Resources\TenantResource;
use App\Modules\UserType\Http\Resources\UserTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->resource->id,
            'name'      => $this->resource->name,
            'email'     => $this->resource->email,
            'cellphone' => $this->resource->cellphone,
            'tenant'    => $this->whenLoaded(
                'tenant',
                fn () => new TenantResource(
                    $this->tenant,
                    $this->resource->user_type_id
                )
            ),
            'documents' => UserDocumentResource::collection($this->whenLoaded('documents')),
            'addresses' => UserAddressResource::collection($this->whenLoaded('addresses')),
            'userType'  => new UserTypeResource($this->whenLoaded('userType')),
            'createdAt' => $this->resource->created_at->format('Y-m-d H:i:s'),
            'updatedAt' => $this->resource->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
