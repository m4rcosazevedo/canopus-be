<?php

namespace App\Services;

use App\Factories\PaymentGatewayFactory;
use App\Models\Tenant;
use App\Models\TenantPayment;
use App\Models\TenantPlan;
use App\Models\TenantSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class TenantSubscriptionService
{
    /**
     * Cria uma nova assinatura e tenta processar o pagamento imediatamente.
     */
    public function subscribe(Tenant $tenant, TenantPlan $plan, string $paymentToken, string $driver = 'stripe'): TenantPayment
    {
        // 1. Cria o registro no banco (Pendente)
        $payment = $this->createPendingSubscription($tenant, $plan, $driver);

        // 2. Tenta cobrar no Gateway
        try {
            $gateway = PaymentGatewayFactory::make($driver);

            $result = $gateway->charge(
                amount: $payment->amount,
                token: $paymentToken,
                options: [
                    'email' => 'tenant_admin@example.com', // Deveria vir do usuário logado
                    'description' => "Assinatura {$plan->name} - {$tenant->name}"
                ]
            );

            if ($result->success) {
                $this->processPaymentSuccess($payment, $result->transactionId);
            } else {
                $payment->update([
                    'status' => 'failed',
                    'gateway_data' => ['error' => $result->errorMessage]
                ]);
            }

            return $payment;

        } catch (Exception $e) {
            $payment->update(['status' => 'failed']);
            throw $e;
        }
    }

    /**
     * Cria registros iniciais (Pendente)
     */
    private function createPendingSubscription(Tenant $tenant, TenantPlan $plan, string $method): TenantPayment
    {
        return DB::transaction(function () use ($tenant, $plan, $method) {
            $subscription = TenantSubscription::create([
                'tenant_id' => $tenant->id,
                'tenant_plan_id' => $plan->id,
                'status' => 'pending',
                'starts_at' => now(),
                'ends_at' => $this->calculateEndDate($plan),
            ]);

            return TenantPayment::create([
                'tenant_id' => $tenant->id,
                'tenant_subscription_id' => $subscription->id,
                'amount' => $plan->price,
                'currency' => 'BRL',
                'payment_method' => $method,
                'status' => 'pending',
            ]);
        });
    }

    /**
     * Processa o sucesso do pagamento e ativa o Tenant/Assinatura.
     */
    public function processPaymentSuccess(TenantPayment $payment, string $transactionId): void
    {
        DB::transaction(function () use ($payment, $transactionId) {
            $payment->update([
                'status' => 'paid',
                'transaction_id' => $transactionId,
                'paid_at' => now(),
            ]);

            $subscription = $payment->subscription;
            if ($subscription) {
                $subscription->update(['status' => 'active']);
            }

            $payment->tenant->update(['status' => 'active']);
        });
    }

    private function calculateEndDate(TenantPlan $plan): Carbon
    {
        $date = now();
        if ($plan->interval === 'monthly') return $date->addMonths($plan->interval_count);
        if ($plan->interval === 'yearly') return $date->addYears($plan->interval_count);
        return $date->addMonth();
    }
}
