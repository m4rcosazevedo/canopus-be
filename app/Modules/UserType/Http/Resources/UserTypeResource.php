<?php

namespace App\Modules\UserType\Http\Resources;

use App\Modules\Permission\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->resource->id,
            'name'        => $this->resource->name,
            'description' => $this->resource->description,
            'visible'     => $this->resource->visible,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions'))
        ];
    }
}
