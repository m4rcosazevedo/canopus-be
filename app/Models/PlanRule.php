<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanRule extends BaseModel
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'key',
        'value',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
