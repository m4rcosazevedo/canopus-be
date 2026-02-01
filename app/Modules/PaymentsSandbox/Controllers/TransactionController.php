<?php

namespace App\Modules\PaymentsSandbox\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\PaymentsSandbox\DTOs\CreateTransactionDTO;
use App\Modules\PaymentsSandbox\Models\SandboxTransaction;
use App\Modules\PaymentsSandbox\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $service
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'payment_method' => 'required|string|in:credit_card,pix,boleto',
            'external_reference' => 'nullable|string',
            'payer_email' => 'nullable|email',
            'webhook_url' => 'nullable|url',
        ]);

        $dto = CreateTransactionDTO::fromRequest($request->all());
        $transaction = $this->service->create($dto);

        return response()->json($transaction, 201);
    }

    public function show($id): JsonResponse
    {
        $transaction = SandboxTransaction::findOrFail($id);
        return response()->json($transaction);
    }

    public function refund($id): JsonResponse
    {
        $transaction = SandboxTransaction::findOrFail($id);

        try {
            $refundedTransaction = $this->service->refund($transaction);
            return response()->json($refundedTransaction);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
