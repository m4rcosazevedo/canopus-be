<?php

namespace App\Modules\Area\Models;

use App\Models\BaseModel;
use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends BaseModel
{
    protected $table = 'areas';

    protected $fillable = [
        'name',
    ];

    /** Relationships */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
