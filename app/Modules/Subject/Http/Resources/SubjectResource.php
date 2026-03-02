<?php

namespace App\Modules\Subject\Http\Resources;

use App\Modules\Area\Http\Resources\AreaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color_hex' => strtoupper($this->color_hex),
            'area' => new AreaResource($this->whenLoaded('area')),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
