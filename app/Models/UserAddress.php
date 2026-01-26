<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends BaseModel
{
    use BelongsToTenant;

    public const DEFAULT_RELATIONS = ['user', 'address.city.state'];

    protected $fillable = [
        'tenant_id',
        'user_id',
        'address_id',
        'number',
        'complement',
        'is_default'
    ];

    /** Relationships */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /** Scopes */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

}
