<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->resource->id,
            "name" => $this->resource->name,
            "description" => $this->resource->description,
            "type" => $this->resource->type,
            "durationInDays" => $this->resource->duration_in_days,
            "maxClassesPerWeek" => $this->resource->max_classes_per_week,
            "totalClassCredits" => $this->resource->total_class_credits,
            "price" => $this->resource->price,
            "allowMakeupClasses" => $this->resource->allow_makeup_classes,
            "maxMakeupPerMonth" => $this->resource->max_makeup_per_month,
            "canFreeze" => $this->resource->can_freeze,
            "maxFreezeDays" => $this->resource->max_freeze_days,
            "status" => $this->resource->status,
            "createdAt" => $this->resource->created_at,
            "updatedAt" => $this->resource->updated_at,
            "services" => $this->resource->services,
            "rules" => $this->resource->rules,
        ];
    }
}
