<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends BaseModel
{
    public const DEFAULT_RELATIONS = ['user', 'address.city.state'];

    protected $fillable = [
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
}
