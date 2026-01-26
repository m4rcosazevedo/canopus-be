<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends BaseModel
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'format',
        'template',
        'endpoint',
        'authenticated',
        'token',
        'parameters',
        'status',
        'path',
        'error_message',
    ];

    protected $casts = [
        'parameters' => 'array',
        'authenticated' => 'boolean',
        'token' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
