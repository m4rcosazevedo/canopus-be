<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;

class AuditLog extends BaseNoAuditableModel
{
    use MassPrunable, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'user_email',
        'event',
        'transaction_id',
        'auditable_id',
        'auditable_type',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Define o critério para registros que devem ser limpos.
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subMonth());
    }

    public function getDiff(): array
    {
        if ($this->event !== 'updated') {
            return [];
        }

        $diff = [];
        $old = $this->old_values ?? [];
        $new = $this->new_values ?? [];

        foreach ($new as $key => $value) {
            if (in_array($key, ['updated_at', 'created_at'])) continue;

            if (!array_key_exists($key, $old) || $old[$key] !== $value) {
                $diff[$key] = [
                    'from' => $old[$key] ?? null,
                    'to' => $value
                ];
            }
        }

        return $diff;
    }
}
