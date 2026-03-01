<?php

namespace App\Modules\Plan\Models;

use App\Models\BaseModel;
use App\Modules\Plan\Enums\PlanPeriod;

class Plan extends BaseModel
{
    protected $table = 'plans';

    protected $fillable = [
        'name',
        'description',
        'price',
        'period',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
        'period' => PlanPeriod::class
    ];
}
