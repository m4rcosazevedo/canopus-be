<?php

namespace App\Models;

use App\Queries\PlanAvailableQuery;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends BaseModel
{
    use BelongsToTenant;

    public const DEFAULT_RELATIONS = ['services', 'rules'];

    protected $fillable = [
        'tenant_id', 'name', 'description', 'type', 'duration_in_days', 'max_classes_per_week',
        'total_class_credits', 'price', 'allow_makeup_classes', 'max_makeup_per_month',
        'can_freeze', 'max_freeze_days', 'status',
    ];

    protected $casts = [
        'price' => 'float',
    ];

    /** Relationships */
    public function services(): HasMany
    {
        return $this->hasMany(PlanService::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(PlanRule::class);
    }

    public function studentPlans(): HasMany
    {
        return $this->hasMany(StudentPlan::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(ClassModel::class);
    }

    /** Scopes */
    public function scopeAvailable($query): Builder
    {
        return PlanAvailableQuery::make();
    }
}
