<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->resource->id,
            "plan"       => PlanResource::make($this->whenLoaded('plan')),
            "user"       => UserResource::make($this->whenLoaded('user')),
            "weekday"    => $this->resource->weekday,
            "startTime"  => $this->resource->start_time,
            "endTime"    => $this->resource->end_time,
            "capacity"   => $this->resource->capacity,
            "room"       => $this->resource->room,
            "status"     => $this->resource->status,
        ];
    }
}
