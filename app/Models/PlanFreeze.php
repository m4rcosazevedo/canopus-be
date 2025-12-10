<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFreeze extends BaseModel
{
    protected $fillable = [
        'student_plan_id',
        'start_date',
        'end_date',
        'total_days',
    ];

    public function studentPlan(): BelongsTo
    {
        return $this->belongsTo(StudentPlan::class);
    }
}
