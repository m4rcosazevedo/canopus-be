<?php

namespace App\Models;

use App\Builders\CustomBuilder;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Filterable;

    const DEFAULT_RELATIONS = [
        'userType',
    ];

    public function newEloquentBuilder($query): CustomBuilder
    {
        return new CustomBuilder($query);
    }

    protected $fillable = ['name', 'email', 'cellphone', 'password', 'user_type_id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Relationships */
    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    /** Attributes */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = remove_extra_spaces($value);
    }
}
