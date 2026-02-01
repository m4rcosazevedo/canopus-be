<?php

namespace App\Modules\PaymentsSandbox\Models;

use App\Modules\PaymentsSandbox\Enums\PaymentMethod;
use App\Modules\PaymentsSandbox\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SandboxTransaction extends Model
{
    use HasFactory;

    protected $table = 'sandbox_transactions';

    protected $fillable = [
        'external_reference',
        'amount',
        'currency',
        'payment_method',
        'status',
        'payer_email',
        'payer_document',
        'card_last_four',
        'pix_qr_code',
        'pix_qr_code_url',
        'boleto_url',
        'boleto_barcode',
        'webhook_url',
        'metadata',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'integer', // Centavos
        'payment_method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];
}
