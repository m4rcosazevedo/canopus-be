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

        return $this->filterRecursive($menus, $userPermissions);
    }

    private function filterRecursive(Collection $menus, array $userPermissions): Collection
    {
        return $menus->filter(function ($menu) use ($userPermissions) {
            // 1. Verifica permissão do menu atual
            if (!$this->canAccessMenu($menu, $userPermissions)) {
                return false;
            }

            // 2. Se o menu tem filhos, aplica o filtro recursivamente
            if ($menu->children->isNotEmpty()) {
                $filteredChildren = $this->filterRecursive($menu->children, $userPermissions);
                $menu->setRelation('children', $filteredChildren);
            }

            // 3. Regra de exibição:
            // Se o menu não tem rota (é um agrupador), ele DEVE ter pelo menos um filho visível.
            if (empty($menu->route)) {
                return $menu->children->isNotEmpty();
            }

            // Se tem rota, exibe normalmente (já passou na verificação de permissão)
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
