<?php

namespace App\Modules\Menu\Services;

use App\Models\User;
use App\Modules\Menu\Repositories\MenuRepository;
use Illuminate\Support\Collection;

class MenuService
{
    public function __construct(
        protected MenuRepository $repository
    ) {}

    public function getMenusForUser(User $user): Collection
    {
        if ($user->user_type_id === 1) {
            return $this->repository->getRootMenus();
        }

        $menus = $this->repository->getRootMenus();

        $user->load(['userType.permissions']);

        $userPermissions = $user->userType ? $user->userType->permissions->pluck('id')->toArray() : [];

        return $menus->filter(function ($menu) use ($userPermissions) {
            if (!$this->canAccessMenu($menu, $userPermissions)) {
                return false;
            }

            $filteredChildren = $menu->children->filter(function ($child) use ($userPermissions) {
                return $this->canAccessMenu($child, $userPermissions);
            })->values();

            $menu->setRelation('children', $filteredChildren);

            if (empty($menu->route)) {
                return $filteredChildren->contains(function ($child) {
                    return !empty($child->route);
                });
            }

            return true;
        })->values();
    }

    /**
     * Filter menus based on user permissions.
     * If a menu has a permission_id, the user must have that permission.
     * If permission_id is null, the menu is visible to everyone.
     */
    private function canAccessMenu($menu, array $userPermissions): bool
    {
        if (!$menu->permission_id) {
            return true;
        }

        return in_array($menu->permission_id, $userPermissions);
    }
}
