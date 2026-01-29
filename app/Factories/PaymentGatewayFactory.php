<?php

namespace App\Factories;

use App\Contracts\PaymentGatewayInterface;
use App\Services\Gateways\MercadoPagoGateway;
use App\Services\Gateways\StripeGateway;
use Exception;

class PaymentGatewayFactory
{
    public static function make(string $driver = null): PaymentGatewayInterface
    {
        $driver = $driver ?? config('services.payment_driver', 'stripe');

        return match ($driver) {
            'stripe' => new StripeGateway(),
            'mercadopago' => new MercadoPagoGateway(),
            default => throw new Exception("Payment driver [$driver] not supported."),
        };
    }
}
