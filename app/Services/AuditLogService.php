<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Http\Resources\AuditLogResource;
use App\Repositories\AuditLogRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditLogService
{
    public function __construct(
        protected AuditLogRepository $repository
    ) {}

    public function list($request): LengthAwarePaginator
    {
        return $this->repository->paginate($request);
    }

    public function show(AuditLog $auditLog): AuditLog
    {
        return $this->repository->find($auditLog);
    }

    public function transactions(int $transactionId): array
    {
        $logs = $this->repository->findByTransaction($transactionId);

        $logs = $logs->map(function ($log) {
            return new AuditLogResource($log);
        });

        return [
            'transaction_id' => $transactionId,
            'total_operations' => $logs->count(),
            'operations' => $logs
        ];
    }
}
