<?php

namespace App\Modules\PaymentsSandbox\Enums;

enum PaymentMethod: string
{
    case CREDIT_CARD = 'credit_card';
    case PIX = 'pix';
    case BOLETO = 'boleto';
}
