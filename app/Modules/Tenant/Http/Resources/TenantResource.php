<?php

namespace App\Modules\Tenant\Http\Resources;

use App\Modules\UserType\Enums\UserTypeIdEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    public function __construct($resource, $userTypeId = null)
    {
        parent::__construct($resource);
        $this->userTypeId = $userTypeId;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'slug' => $this->resource->slug,
            'domain' => $this->resource->domain,
            'configs' => $this->when(
                in_array(
                    $this->userTypeId,
                    [
                        UserTypeIdEnum::ROOT->value,
                        UserTypeIdEnum::ADMINISTRATOR->value,
                    ],
                    true
                ),
                [
                    'hasSubscription' => $this->hasSubscription(),
                    'isSubscriptionActive' => $this->isActive()
                ]
            )
        ];
    }
}
