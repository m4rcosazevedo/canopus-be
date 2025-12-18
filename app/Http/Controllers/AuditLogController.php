<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditLog\ListAuditLogRequest;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuditLogController
{

    public function __construct(
        protected AuditLogService $service
    ){}

    public function index(ListAuditLogRequest $request): AnonymousResourceCollection
    {
        $logs = $this->service->list($request);
        return AuditLogResource::collection($logs);
    }

    public function show(AuditLog $auditLog): AuditLogResource
    {
        $log = $this->service->show($auditLog);
        return new AuditLogResource($log);
    }

    public function showTransaction($transactionId): JsonResponse
    {
        $transactions = $this->service->transactions((int) $transactionId);

        return response()->json($transactions);
    }
}
