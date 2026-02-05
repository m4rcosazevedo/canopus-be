<?php

namespace App\Modules\Tenant\Http\Resources;

class TenantPlanPublicResource extends TenantPlanResource
{
    public function toArray($request): array
    {
        return collect(parent::toArray($request))
            ->except([
                'interval_count',
                'features',
            ])
            ->toArray();
    }
}
