<?php

namespace App\Modules\Area\Models;

use App\Models\BaseModel;

class Area extends BaseModel
{
    protected $table = 'areas';

    protected $fillable = [
        'name',
    ];
}
