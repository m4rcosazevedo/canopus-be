<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanService extends BaseModel
{
    protected $fillable = [
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
