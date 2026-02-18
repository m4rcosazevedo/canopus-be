<?php

namespace App\Models;

use App\Modules\City\Models\City;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends BaseModel
{
    protected $fillable = [
        'zip_code',
        'street_type',
        'street_name',
        'district',
        'city_id'
    ];

    /** Relationships */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /** Attributes */
    public function setDistrictAttribute(string $value): void
    {
        $this->attributes['district'] = remove_extra_spaces(trim($value));
    }

    public function setStreetNameAttribute(string $value): void
    {
        $this->attributes['street_name'] = remove_extra_spaces(trim($value));
    }
}
