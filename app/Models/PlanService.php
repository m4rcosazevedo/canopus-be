<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanService extends BaseModel
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'name',
        'description',
        'extra_price',
        'limit_per_month',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
