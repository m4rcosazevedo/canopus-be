<?php

namespace App\Modules\PaymentsSandbox\DTOs;

use App\Modules\PaymentsSandbox\Enums\PaymentMethod;

readonly class CreateTransactionDTO
{
    public function __construct(
        public int $amount,
        public PaymentMethod $paymentMethod,
        public ?string $externalReference = null,
        public ?string $payerEmail = null,
        public ?string $payerDocument = null,
        public ?string $cardNumber = null,
        public ?string $cardHolder = null,
        public ?string $cardExpiration = null,
        public ?string $cardCvv = null,
        public ?string $webhookUrl = null,
        public array $metadata = [],
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            amount: $data['amount'],
            paymentMethod: PaymentMethod::from($data['payment_method']),
            externalReference: $data['external_reference'] ?? null,
            payerEmail: $data['payer_email'] ?? null,
            payerDocument: $data['payer_document'] ?? null,
            cardNumber: $data['card_number'] ?? null,
            cardHolder: $data['card_holder'] ?? null,
            cardExpiration: $data['card_expiration'] ?? null,
            cardCvv: $data['card_cvv'] ?? null,
            webhookUrl: $data['webhook_url'] ?? null,
            metadata: $data['metadata'] ?? [],
        );
    }
}
