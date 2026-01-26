<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFreeze extends BaseModel
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
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
