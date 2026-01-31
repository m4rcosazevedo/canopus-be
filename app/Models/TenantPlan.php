<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class TenantPlan extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'interval', 'interval_count', 'features', 'is_active',
        'features_available', 'popular'
    ];

    protected $casts = [
        'price' => 'float',
        'features' => 'array',
        'is_active' => 'boolean',
        'popular' => 'boolean',
    ];

    /** Scopes */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Attributes */
    public function getFeaturesAvailableAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        return array_values(
            array_filter(
                array_map('trim', explode(';', $value)),
                fn ($item) => $item !== ''
            )
        );
    }

    public function setFeaturesAvailableAttribute($value)
    {
        if (is_array($value)) {
            $cleaned = array_values(
                array_filter(
                    array_map('trim', $value),
                    fn ($item) => $item !== ''
                )
            );

            $this->attributes['features_available'] = empty($cleaned)
                ? null
                : implode(';', $cleaned);

            return;
        }

        $this->attributes['features_available'] = empty(trim($value))
            ? null
            : trim($value);
    }



    protected static function booted(): void
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
