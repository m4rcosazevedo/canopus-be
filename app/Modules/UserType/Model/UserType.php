<?php

namespace App\Modules\UserType\Model;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserType extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'visible'
    ];

    protected $casts = [
        'visible' => 'boolean'
    ];

    /** Relationships */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Scopes */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visible', true);
    }

    public function scopeOrderByDescription(Builder $query): Builder
    {
        return $query->orderBy('description');
    }

    /** Attributes */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::slug($value);
    }
}
