<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserType extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'visible'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
