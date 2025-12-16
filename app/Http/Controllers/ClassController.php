<?php

namespace App\Http\Controllers;

use App\Http\Requests\Class\StoreClassRequest;
use App\Http\Requests\Class\UpdateClassRequest;
use App\Http\Resources\ClassResource;
use App\Models\ClassModel;
use App\Services\ClassService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ClassController extends Controller
{
    public function __construct(
        protected ClassService $service
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $classes = $this->service->list();
        return ClassResource::collection($classes);
    }

    public function store(StoreClassRequest $request): JsonResponse
    {
        $class = $this->service->create($request->validated());
        return (new ClassResource($class))
            ->response()
            ->setStatusCode(201);
    }

    public function show(ClassModel $class): ClassResource
    {
        $class = $this->service->show($class);
        return ClassResource::make($class);
    }

    public function update(UpdateClassRequest $request, ClassModel $class): ClassResource
    {
        $class = $this->service->update($class, $request->validated());
        return new ClassResource($class);
    }

    public function destroy(ClassModel $class): Response
    {
        $this->service->delete($class);
        return response()->noContent();
    }

    public function availableClasses($planId)
    {
        $classes = $this->service->availableByPlan($planId);
        return ClassResource::collection($classes);
    }
}
