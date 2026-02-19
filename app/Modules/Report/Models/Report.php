<?php

namespace App\Modules\Report\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends BaseModel
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
        'token' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
