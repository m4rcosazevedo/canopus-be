<?php

namespace App\DTOs;

class PaymentResult
{
    public function __construct(
        public bool $success,
        public string $transactionId,
        public string $status,
        public float $amount,
        public string $currency,
        public array $originalData = [],
        public ?string $errorMessage = null
    ) {}
}
