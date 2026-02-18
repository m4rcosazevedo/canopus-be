<?php

namespace App\Modules\City\Models;

use App\Models\Address;
use App\Models\BaseModel;
use App\Modules\State\Model\State;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends BaseModel
{
    protected $fillable = ['state_id', 'name', 'ibge_code'];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
