<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuditLogController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $logs = AuditLog::query()
            ->when($request->transaction_id, fn($q) => $q->where('transaction_id', $request->transaction_id))
            ->when($request->email, fn($q) => $q->where('user_email', $request->email))
            ->when($request->event, fn($q) => $q->where('event', $request->event))
            ->latest()
            ->paginate($request->get('per_page', 20));

        return AuditLogResource::collection($logs);
    }

    public function show(AuditLog $auditLog): AuditLogResource
    {
        return new AuditLogResource($auditLog);
    }

    public function showTransaction($transactionId): JsonResponse
    {
        $logs = AuditLog::where('transaction_id', $transactionId)
            ->oldest()
            ->get()
            ->map(function ($log) {
                return new AuditLogResource($log);
            });

        return response()->json([
            'transaction_id' => $transactionId,
            'total_operations' => $logs->count(),
            'operations' => $logs
        ]);
    }
}
