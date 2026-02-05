<?php

namespace App\Modules\Tenant\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
//        'status',
//        'trial_ends_at'
    ];

//    protected $casts = [
//        'trial_ends_at' => 'date',
//    ];

    public function subscription(): HasOne
    {
        return $this->hasOne(TenantSubscription::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(TenantPayment::class);
    }

    public function isActive(): bool
    {
        if ($this->status === 'active') {
            return true;
        }

        if ($this->onTrial()) {
            return true;
        }

        // Verifica se tem assinatura ativa
        return $this->subscription && $this->subscription->isActive();
    }

    public function hasSubscription(): bool
    {
        return !empty($this->subscription);
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->isDirty('name') || empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->name);
            }
        });
    }

    private static function generateUniqueSlug(string $name): string
    {
        $hash = substr(md5($name . microtime()), 0, 6);
        return Str::slug($name) . '-' . $hash;
    }

}
