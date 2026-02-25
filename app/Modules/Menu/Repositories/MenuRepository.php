<?php

namespace App\Modules\Menu\Repositories;

use App\Modules\Menu\Models\Menu;
use Illuminate\Database\Eloquent\Collection;

class MenuRepository
{
    public function getRootMenus(): Collection
    {
        return Menu::query()
            ->whereNull('parent_id')
            ->where('visible', true)
            ->with(['children' => function ($query) {
                $query->where('visible', true)->orderBy('order');
            }, 'permission'])
            ->orderBy('order')
            ->get();
    }
}
