<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
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
        'token' => 'encrypted', // Encrypt the token for security
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
