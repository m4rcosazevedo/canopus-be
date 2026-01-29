<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'tenant_subscription_id', 'amount', 'currency',
        'payment_method', 'status', 'transaction_id', 'gateway_data', 'paid_at'
    ];

    protected $casts = [
        'amount' => 'float',
        'gateway_data' => 'array',
        'paid_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(TenantSubscription::class, 'tenant_subscription_id');
    }
}
