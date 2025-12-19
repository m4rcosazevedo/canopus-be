<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends BaseModel
{
    protected $fillable = ['name', 'abbr', 'ibge_code'];

    /** Relationships */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /** Attributes */
    public function setAbbrAttribute($value)
    {
        $this->attributes['abbr'] = strtoupper(trim($value));
    }
}
