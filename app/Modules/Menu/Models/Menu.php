<?php

namespace App\Modules\Menu\Models;

use App\Models\BaseModel;
use App\Modules\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends BaseModel
{
    protected $fillable = [
        'name',
        'route',
        'icon',
        'parent_id',
        'permission_id',
        'order',
        'visible',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
