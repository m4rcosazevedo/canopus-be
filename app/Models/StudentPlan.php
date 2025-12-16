<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentPlan extends BaseModel
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'remaining_credits',
        'status',
        'freeze_until',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function freezes(): HasMany
    {
        return $this->hasMany(PlanFreeze::class);
    }
}
