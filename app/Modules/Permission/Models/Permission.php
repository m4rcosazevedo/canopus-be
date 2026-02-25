<?php

namespace App\Modules\Permission\Models;

use App\Models\BaseModel;
use App\Modules\UserType\Model\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Permission extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /** Relationships */
    public function userTypes(): BelongsToMany
    {
        return $this->belongsToMany(UserType::class, 'permission_user_type');
    }

    /** Attributes */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::slugWithDot($value);
    }
}
