<?php

namespace App\Modules\Tenant\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'slug' => $this->resource->slug,
            'description' => $this->resource->description,
            'price' => $this->resource->price,
            'interval' => $this->resource->interval,
            'interval_count' => $this->resource->interval_count,
            'features' => $this->resource->features,
            'features_available' => $this->resource->features_available,
            'popular' => $this->resource->popular,
        ];
    }
}
