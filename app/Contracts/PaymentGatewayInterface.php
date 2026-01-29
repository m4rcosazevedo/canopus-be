<?php

namespace App\Contracts;

use App\DTOs\PaymentResult;

interface PaymentGatewayInterface
{
    /**
     * Processa um pagamento único (Cobrança no cartão, Pix, etc).
     *
     * @param float $amount Valor a ser cobrado
     * @param string $token Token do cartão ou identificador da fonte de pagamento
     * @param array $options Opções adicionais (email, descrição, metadata)
     */
    public function charge(float $amount, string $token, array $options = []): PaymentResult;

    /**
     * Cria uma assinatura recorrente (se o gateway suportar nativamente).
     * Se não suportar, o sistema pode gerenciar a recorrência cobrando mensalmente via `charge`.
     */
    public function createSubscription(string $planId, string $customerToken, array $options = []): PaymentResult;
}
