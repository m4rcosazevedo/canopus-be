<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentPlanResource;
use App\Services\EnrollService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Enroll\CreateEnrollRequest;

class EnrollmentController extends Controller
{
    public function __construct(
        protected EnrollService $service,
    ) {}

    /**
     * @throws ValidationException
     */
    public function enroll(CreateEnrollRequest $request): JsonResponse
    {
        $enrollment = $this->service->enroll($request);

        return response()->json([
            'message' => 'Registration successful!',
            'enrollment' => new StudentPlanResource($enrollment)
        ], Response::HTTP_CREATED);
    }
}
