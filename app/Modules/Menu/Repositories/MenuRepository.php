<?php

namespace App\Modules\Menu\Repositories;

use App\Modules\Menu\Models\Menu;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MenuRepository
{
    public function paginate(): LengthAwarePaginator
    {
        return Menu::query()
            ->with(['parent'])
            ->paginate();
    }

    public function all(): Collection
    {
        return Menu::query()
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): Menu
    {
        return Menu::create($data);
    }

    public function findById(int $id): Menu
    {
        return Menu::with(['permission', 'parent'])
            ->findOrFail($id);
    }

    public function update(int $id, array $data): Menu
    {
        $menu = $this->findById($id);
        $menu->update($data);
        return $menu;
    }

    public function delete(int $id): void
    {
        $menu = $this->findById($id);
        $menu->delete();
    }

    public function getRootMenus(): Collection
    {
        return Menu::query()
            ->whereNull('parent_id')
            ->where('visible', true)
            ->with($this->buildEagerLoadRelations(depth: 5))
            ->orderBy('order')
            ->get();
    }

    private function buildEagerLoadRelations(int $depth): array
    {
        $visibleAndOrdered = fn (Builder $query) => $query
            ->where('visible', true)
            ->orderBy('order');

        $relations = [];
        $path = 'children';

        for ($i = 0; $i < $depth; $i++) {
            $relations[$path] = $visibleAndOrdered;
            $relations["{$path}.permission"] = fn (Builder $query) => $query;

            $path .= '.children';
        }

        return $relations;
    }
}
