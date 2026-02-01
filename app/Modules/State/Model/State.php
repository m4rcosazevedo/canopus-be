<?php

namespace App\Modules\State\Model;

use App\Models\BaseModel;
use App\Models\City;
use App\Models\UserDocument;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends BaseModel
{
    protected $fillable = ['name', 'abbr', 'ibge_code'];

    /** Relationships */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function userDocuments(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }

    /** Attributes */
    public function setAbbrAttribute($value)
    {
        $this->attributes['abbr'] = strtoupper(trim($value));
    }
}
