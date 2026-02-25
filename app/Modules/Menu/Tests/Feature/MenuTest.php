<?php

namespace App\Modules\Menu\Tests\Feature;

use App\Models\User;
use App\Modules\Menu\Models\Menu;
use App\Modules\Permission\Models\Permission;
use App\Modules\UserType\Model\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_menus_for_user()
    {
        $userType = UserType::create(['name' => 'Admin', 'description' => 'Admin User', 'visible' => true]);
        $user = User::factory()->create(['user_type_id' => $userType->id]);

        $permission1 = Permission::create(['name' => 'view_dashboard', 'description' => 'View Dashboard']);
        $permission2 = Permission::create(['name' => 'view_admin', 'description' => 'View Admin']);

        $userType->permissions()->attach($permission1->id);

        $menu1 = Menu::create(['name' => 'Dashboard', 'permission_id' => $permission1->id, 'visible' => true]);
        $menu2 = Menu::create(['name' => 'Settings', 'visible' => true]); // Public menu
        $menu3 = Menu::create(['name' => 'Admin', 'permission_id' => $permission2->id, 'visible' => true]); // User doesn't have this permission

        $response = $this->actingAs($user)->getJson('/api/menus');

        $response->assertStatus(200)
            ->assertJsonCount(2) // Dashboard and Settings
            ->assertJsonFragment(['name' => 'Dashboard'])
            ->assertJsonFragment(['name' => 'Settings'])
            ->assertJsonMissing(['name' => 'Admin']);
    }
}
