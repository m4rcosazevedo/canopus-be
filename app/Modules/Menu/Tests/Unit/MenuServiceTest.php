<?php

namespace App\Modules\Menu\Tests\Unit;

use App\Models\User;
use App\Modules\Menu\Models\Menu;
use App\Modules\Menu\Repositories\MenuRepository;
use App\Modules\Menu\Services\MenuService;
use App\Modules\Permission\Models\Permission;
use App\Modules\UserType\Model\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_menus_based_on_permissions()
    {
        $userType = UserType::create(['name' => 'Admin', 'description' => 'Admin User', 'visible' => true]);
        $user = User::factory()->create(['user_type_id' => $userType->id]);

        $permission1 = Permission::create(['name' => 'view_dashboard', 'description' => 'View Dashboard']);
        $permission2 = Permission::create(['name' => 'view_admin', 'description' => 'View Admin']);

        $userType->permissions()->attach($permission1->id);

        $menu1 = Menu::create(['name' => 'Dashboard', 'permission_id' => $permission1->id, 'visible' => true]);
        $menu2 = Menu::create(['name' => 'Settings', 'visible' => true]); // Public menu
        $menu3 = Menu::create(['name' => 'Admin', 'permission_id' => $permission2->id, 'visible' => true]); // User doesn't have this permission

        $service = new MenuService(new MenuRepository());
        $menus = $service->getMenusForUser($user);

        $this->assertCount(2, $menus);
        $this->assertTrue($menus->contains('name', 'Dashboard'));
        $this->assertTrue($menus->contains('name', 'Settings'));
        $this->assertFalse($menus->contains('name', 'Admin'));
    }
}
