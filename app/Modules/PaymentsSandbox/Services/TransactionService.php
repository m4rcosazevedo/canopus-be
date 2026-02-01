<?php

namespace App\Modules\PaymentsSandbox\Services;

use App\Modules\PaymentsSandbox\DTOs\CreateTransactionDTO;
use App\Modules\PaymentsSandbox\Enums\PaymentMethod;
use App\Modules\PaymentsSandbox\Enums\PaymentStatus;
use App\Modules\PaymentsSandbox\Jobs\ProcessTransactionJob;
use App\Modules\PaymentsSandbox\Jobs\SendWebhookJob;
use App\Modules\PaymentsSandbox\Models\SandboxTransaction;
use Illuminate\Support\Str;

class TransactionService
{
    public function create(CreateTransactionDTO $dto): SandboxTransaction
    {
        $transaction = new SandboxTransaction();
        $transaction->amount = $dto->amount;
        $transaction->payment_method = $dto->paymentMethod;
        $transaction->external_reference = $dto->externalReference;
        $transaction->payer_email = $dto->payerEmail;
        $transaction->payer_document = $dto->payerDocument;
        $transaction->webhook_url = $dto->webhookUrl;
        $transaction->metadata = $dto->metadata;
        $transaction->status = PaymentStatus::PENDING;

        if ($dto->paymentMethod === PaymentMethod::CREDIT_CARD) {
            $transaction->card_last_four = substr($dto->cardNumber, -4);
        }

        if ($dto->paymentMethod === PaymentMethod::PIX) {
            $transaction->pix_qr_code = Str::random(64);
            $transaction->pix_qr_code_url = 'https://sandbox.api/qr/' . Str::random(10);
        }

        $transaction->save();

        // Simula processamento assíncrono com delay
        ProcessTransactionJob::dispatch($transaction)->delay(now()->addSeconds(rand(3, 10)));

        return $transaction;
    }

    public function process(SandboxTransaction $transaction): void
    {
        // Lógica de simulação de aprovação/rejeição
        // Exemplo simples: se o valor terminar em 99 centavos, falha.
        // Caso contrário, aprova.

        // Antifraude simulado
        if ($transaction->amount % 100 === 99) {
            $transaction->status = PaymentStatus::FAILED;
            $transaction->metadata = array_merge($transaction->metadata ?? [], ['failure_reason' => 'antifraud_rejection']);
        } else {
            $transaction->status = PaymentStatus::PAID;
            $transaction->processed_at = now();
        }

        $transaction->save();

        // Disparar webhook se configurado
        if ($transaction->webhook_url) {
             SendWebhookJob::dispatch($transaction);
        }
    }

    public function refund(SandboxTransaction $transaction): SandboxTransaction
    {
        if ($transaction->status !== PaymentStatus::PAID) {
            throw new \Exception("Transaction cannot be refunded.");
        }

        $transaction->status = PaymentStatus::REFUNDED;
        $transaction->save();

        // Disparar webhook de refund
        if ($transaction->webhook_url) {
             SendWebhookJob::dispatch($transaction);
        }

        return $transaction;
    }
}
