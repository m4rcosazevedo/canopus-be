<?php

namespace App\Modules\Subject\Models;

use App\Models\BaseModel;
use App\Modules\Area\Models\Area;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subject extends BaseModel
{
    protected $fillable = [
        'name',
        'area_id',
        'color_hex'
    ];

    /** Relationships */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

}
