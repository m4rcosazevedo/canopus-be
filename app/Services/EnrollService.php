<?php

namespace App\Services;

use App\Models\StudentPlan;
use App\Repositories\StudentPlanRepository;
use App\Repositories\PlanRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class EnrollService
{
    public function __construct(
        protected PlanRepository        $planRepository,
        protected StudentPlanRepository $studentPlanRepository,
    ) {}

    /**
     * @throws ValidationException
     */
    public function enroll($request): JsonResponse|StudentPlan
    {
        $planId = (int) $request->input('plan_id');
        $startDate = $request->input('start_date');
        $userId = $request->input('user_id');

        $plan = $this->planRepository->findById($planId, ['classes']);

        if (!$plan->status) {
            return response()->json([
                'message' => 'This plan is not available.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $hasAvailableClasses = $plan->classes->contains(function ($class) {
            return $class->registrations_count < $class->capacity;
        });

        if (!$hasAvailableClasses) {
            throw ValidationException::withMessages([
                'plan_id' => trans('enroll.errors.no_available_times')
            ]);
        }

        $start = $startDate;
        $end = $plan->duration_in_days
            ? date('Y-m-d', strtotime("+{$plan->duration_in_days} days", strtotime($start)))
            : null;

        $studentPlan = $this->studentPlanRepository->create([
            'user_id' => $userId,
            'plan_id' => $plan->id,
            'start_date' => $start,
            'end_date' => $end,
            'remaining_credits' => $plan->total_class_credits,
            'status' => true
        ]);

        return $this->studentPlanRepository->loadRelationship($studentPlan);
    }
}
