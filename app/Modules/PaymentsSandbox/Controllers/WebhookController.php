<?php

namespace App\Modules\PaymentsSandbox\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\PaymentsSandbox\Models\SandboxTransaction;
use App\Modules\PaymentsSandbox\Jobs\SendWebhookJob;
use Illuminate\Http\JsonResponse;

class WebhookController extends Controller
{
    public function trigger($transactionId): JsonResponse
    {
        $transaction = SandboxTransaction::findOrFail($transactionId);

        if (!$transaction->webhook_url) {
            return response()->json(['message' => 'No webhook URL configured for this transaction'], 400);
        }

        // Dispara o job de webhook manualmente
        SendWebhookJob::dispatch($transaction);

        return response()->json(['message' => 'Webhook triggered']);
    }
}
