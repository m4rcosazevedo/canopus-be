<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentResult;
use Exception;
use Illuminate\Support\Facades\Log;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        // \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function charge(float $amount, string $token, array $options = []): PaymentResult
    {
        try {
            // Simulação da chamada ao Stripe
            // $charge = \Stripe\Charge::create([
            //     'amount' => $amount * 100, // Cents
            //     'currency' => 'brl',
            //     'source' => $token,
            //     'description' => $options['description'] ?? 'Assinatura SaaS',
            // ]);

            // Mock para desenvolvimento sem SDK
            $mockSuccess = true;

            if ($mockSuccess) {
                return new PaymentResult(
                    success: true,
                    transactionId: 'ch_' . uniqid(),
                    status: 'succeeded',
                    amount: $amount,
                    currency: 'BRL',
                    originalData: ['id' => 'ch_fake', 'status' => 'succeeded']
                );
            }

        } catch (Exception $e) {
            Log::error('Stripe Charge Error: ' . $e->getMessage());

            return new PaymentResult(
                success: false,
                transactionId: '',
                status: 'failed',
                amount: $amount,
                currency: 'BRL',
                errorMessage: $e->getMessage()
            );
        }

        return new PaymentResult(false, '', 'failed', $amount, 'BRL', [], 'Unknown error');
    }

    public function createSubscription(string $planId, string $customerToken, array $options = []): PaymentResult
    {
        // Implementação de assinatura nativa do Stripe seria aqui
        throw new Exception("Not implemented yet");
    }
}
