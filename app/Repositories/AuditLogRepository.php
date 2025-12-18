<?php

namespace App\Repositories;

use App\Models\AuditLog;
use App\Filters\AuditLogFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditLogRepository
{
    public function paginate($request): LengthAwarePaginator
    {
        return AuditLog::query()
            ->filter(new AuditLogFilter($request))
            ->latest()
            ->paginate();
    }

    public function find(AuditLog $auditLog): AuditLog
    {
        return $auditLog;
    }

    public function findByTransaction(int $transactionId): Collection
    {
        return AuditLog::query()
            ->where('transaction_id', $transactionId)
            ->oldest()
            ->get();
    }


}
