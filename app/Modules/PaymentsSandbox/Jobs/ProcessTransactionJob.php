<?php

namespace App\Modules\PaymentsSandbox\Jobs;

use App\Modules\PaymentsSandbox\Models\SandboxTransaction;
use App\Modules\PaymentsSandbox\Services\TransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public SandboxTransaction $transaction
    ) {}

    public function handle(TransactionService $service): void
    {
        $service->process($this->transaction);
    }
}
