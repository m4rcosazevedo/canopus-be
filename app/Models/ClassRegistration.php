<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassRegistration extends BaseModel
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'class_id', 'student_id', 'student_plan_id', 'status', 'checked_in_at'];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studentPlan(): BelongsTo
    {
        return $this->belongsTo(StudentPlan::class);
    }

}
