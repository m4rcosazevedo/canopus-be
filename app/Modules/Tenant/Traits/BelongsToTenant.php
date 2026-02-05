<?php

namespace App\Modules\Tenant\Traits;

use App\Modules\Tenant\Models\Tenant;
use App\Modules\Tenant\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (
                app()->bound('tenant_id') &&
                app('tenant_id')
            ) {
                $model->tenant_id = app('tenant_id');
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
