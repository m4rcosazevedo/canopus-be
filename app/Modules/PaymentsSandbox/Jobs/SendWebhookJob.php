<?php

namespace App\Modules\PaymentsSandbox\Jobs;

use App\Modules\PaymentsSandbox\Models\SandboxTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(
        public SandboxTransaction $transaction
    ) {}

    public function handle(): void
    {
        if (!$this->transaction->webhook_url) {
            return;
        }

        $payload = [
            'event' => 'transaction.updated',
            'data' => $this->transaction->toArray(),
            'timestamp' => now()->toIso8601String(),
        ];

        try {
            $response = Http::timeout(5)
                ->post($this->transaction->webhook_url, $payload);

            if ($response->failed()) {
                Log::warning("Webhook failed for transaction {$this->transaction->id}: " . $response->status());
                $this->release(10); // Retry manual se necessário, mas o $tries cuida disso
                throw new \Exception("Webhook failed with status " . $response->status());
            }

            Log::info("Webhook sent successfully for transaction {$this->transaction->id}");

        } catch (\Exception $e) {
            Log::error("Webhook error for transaction {$this->transaction->id}: " . $e->getMessage());
            throw $e; // Força o retry do Laravel
        }
    }
}
