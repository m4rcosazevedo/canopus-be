<?php

namespace App\Http\Controllers;

use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PlanController extends Controller
{
    public function __construct(
        protected PlanService $service
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $plans = $this->service->list();

        return PlanResource::collection($plans);
    }

    public function store(StorePlanRequest $request): JsonResponse
    {
        $plan = $this->service->create($request->validated());

        return (new PlanResource($plan))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Plan $plan): PlanResource
    {
        return new PlanResource(
            $this->service->show($plan)
        );
    }

    public function update(UpdatePlanRequest $request, Plan $plan): PlanResource
    {
        $plan = $this->service->update($plan, $request->validated());

        return new PlanResource($plan);
    }

    public function destroy(Plan $plan): Response
    {
        $this->service->delete($plan);

        return response()->noContent();
    }

    public function available(): AnonymousResourceCollection
    {
        $plans = $this->service->listAvailable();

        return PlanResource::collection($plans);
    }
}
