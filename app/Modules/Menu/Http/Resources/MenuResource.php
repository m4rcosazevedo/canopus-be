<?php

namespace App\Modules\Menu\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->resource->id,
            'name'          => $this->resource->name,
            'route'         => $this->resource->route,
            'icon'          => $this->resource->icon,
            'order'         => $this->resource->order,
            'visible'       => $this->resource->visible,
            'parent_id'     => $this->resource->parent_id,
            'permission_id' => $this->resource->permission_id,
            'children'      => MenuResource::collection($this->whenLoaded('children')),
        ];
    }
}
