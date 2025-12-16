<?php

namespace App\Repositories;


use App\Models\StudentPlan;

class StudentPlanRepository
{
    public function create(array $data): StudentPlan
    {
        return StudentPlan::create($data);
    }

    public function loadRelationship(StudentPlan $studentPlan): StudentPlan
    {
        return $studentPlan->load(['user', 'plan']);
    }
}
