<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassRegistration extends BaseModel
{
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
