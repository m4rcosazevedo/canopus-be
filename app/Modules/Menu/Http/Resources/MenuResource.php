<?php

namespace App\Modules\Menu\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'route' => $this->route,
            'icon' => $this->icon,
            'order' => $this->order,
            'children' => MenuResource::collection($this->whenLoaded('children')),
        ];
    }
}
