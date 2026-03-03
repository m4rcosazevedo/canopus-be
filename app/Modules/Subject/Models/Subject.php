<?php

namespace App\Modules\Subject\Models;

use App\Models\BaseModel;
use App\Modules\Area\Models\Area;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Subject extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'area_id',
        'color_hex'
    ];

    /** Relationships */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /** Attributes */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
}
