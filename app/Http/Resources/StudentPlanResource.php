<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'plan'              => PlanResource::make($this->whenLoaded('plan')),
            'user'              => UserResource::make($this->whenLoaded('user')),
            'startDate'        => $this->resource->start_date,
            'endDate'          => $this->resource->end_date,
            'remainingCredits' => $this->resource->remaining_credits,
            'status'            => $this->resource->status,
            'updated_at'        => $this->resource->updated_at,
            'created_at'        => $this->resource->created_at
        ];
    }
}
