<?php

namespace App\Rules;

use App\Models\StudentPlan;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StudentHasNoActivePlan implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hasActivePlan = StudentPlan::where('user_id', $value)
            ->where('status', 'active')
            ->exists();

        if ($hasActivePlan) {
            $fail(trans('enroll.errors.student_has_active_plan'));
        }
    }
}
