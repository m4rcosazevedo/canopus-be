<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanRule extends BaseModel
{
    protected $fillable = [
        'plan_id',
        'key',
        'value',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
