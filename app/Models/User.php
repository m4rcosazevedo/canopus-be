<?php

namespace App\Models;

use App\Builders\CustomBuilder;
use App\Modules\UserType\Model\UserType;
use App\Traits\Auditable;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Filterable, Auditable;

    const DEFAULT_RELATIONS = [
        'userType',
        'documents.state',
        'documents.documentType',
        'addresses.address.city.state',
    ];

    protected array $auditExclude = ['password', 'remember_token'];

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

    public function documents(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
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

    /** Methods */
    public function hasAllPermissions(array $permissions): bool
    {
        if (empty($permissions)) {
            return false;
        }

        $userPermissions = $this->getCachedPermissions();

        return count(array_intersect($permissions, $userPermissions)) === count($permissions);
    }

    protected function getCachedPermissions(): array
    {
        static $permissions;

        if ($permissions) {
            return $permissions;
        }

        return $permissions = Cache::remember("permissions_role_{$this->user_type_id}", 3600, function () {
            return $this->userType
                ? $this->userType->permissions()->pluck('name')->toArray()
                : [];
        });
    }
}
