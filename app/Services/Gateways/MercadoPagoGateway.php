<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentResult;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MercadoPagoGateway implements PaymentGatewayInterface
{
    protected string $accessToken;

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token', 'TEST-TOKEN');
    }

    public function charge(float $amount, string $token, array $options = []): PaymentResult
    {
        try {
            // Exemplo de chamada via HTTP Client do Laravel (sem SDK)
            // $response = Http::withToken($this->accessToken)->post('https://api.mercadopago.com/v1/payments', [
            //     'transaction_amount' => $amount,
            //     'token' => $token,
            //     'description' => $options['description'] ?? 'SaaS Subscription',
            //     'installments' => 1,
            //     'payment_method_id' => $options['payment_method_id'] ?? 'visa',
            //     'payer' => [
            //         'email' => $options['email']
            //     ]
            // ]);

            // Mock
            return new PaymentResult(
                success: true,
                transactionId: 'mp_' . uniqid(),
                status: 'approved',
                amount: $amount,
                currency: 'BRL',
                originalData: ['status' => 'approved']
            );

        } catch (Exception $e) {
            Log::error('MercadoPago Error: ' . $e->getMessage());

            return new PaymentResult(
                success: false,
                transactionId: '',
                status: 'rejected',
                amount: $amount,
                currency: 'BRL',
                errorMessage: $e->getMessage()
            );
        }
    }

    public function createSubscription(string $planId, string $customerToken, array $options = []): PaymentResult
    {
        throw new Exception("Not implemented yet");
    }
}
