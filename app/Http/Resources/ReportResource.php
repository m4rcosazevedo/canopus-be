<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'name' => $this->resource->name,
            'title' => $this->resource->title,
            'path' => basename($this->resource->path),
            'status' => $this->resource->status,
            'format' => $this->resource->format,
            'createdAt' => $this->resource->created_at,
            'updatedAt' => $this->resource->updated_at,
        ];
    }
}
