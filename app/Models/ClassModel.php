<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends BaseModel
{
    use BelongsToTenant;

    public const DEFAULT_RELATIONS = ['plan', 'user'];
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $table = 'classes';

    protected $fillable = [
        'tenant_id', 'plan_id', 'user_id', 'weekday', 'start_time',
        'end_time', 'capacity', 'room', 'status',
    ];

    protected $appends = ['registrations_count'];

    /** Relationships */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ClassRegistration::class, 'class_id', 'id');
    }

    /** Scopes */
    public function scopeAvailable($query)
    {
        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->withCount('registrations')
            ->havingRaw('registrations_count < capacity');
    }

    /** Attributes */
    public function getRegistrationsCountAttribute()
    {
        if (array_key_exists('registrations_count', $this->attributes)) {
            return $this->attributes['registrations_count'];
        }

        return $this->registrations()->count();
    }
}
