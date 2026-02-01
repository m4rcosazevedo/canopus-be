<?php

namespace App\Modules\PaymentsSandbox\Models;

use Illuminate\Database\Eloquent\Model;

class SandboxIdempotency extends Model
{
    protected $table = 'sandbox_idempotencies';

    protected $fillable = [
        'key',
        'response_body',
        'response_code',
    ];

    protected $casts = [
        'response_body' => 'array',
    ];
}
