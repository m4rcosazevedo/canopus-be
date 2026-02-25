<?php

namespace App\Modules\Menu\Tests\Feature;

use App\Models\User;
use App\Modules\Menu\Models\Menu;
use App\Modules\UserType\Model\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userType = UserType::create(['name' => 'Admin', 'description' => 'Admin User', 'visible' => true]);
        $this->user = User::factory()->create(['user_type_id' => $this->userType->id]);
    }

    public function test_can_list_all_menus()
    {
        Menu::create(['name' => 'Menu 1', 'visible' => true]);
        Menu::create(['name' => 'Menu 2', 'visible' => true]);

        $response = $this->actingAs($this->user)->getJson('/api/menus');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_menu()
    {
        $data = [
            'name' => 'New Menu',
            'route' => '/new-menu',
            'icon' => 'icon-new',
            'order' => 1,
            'visible' => true,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/menus', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'New Menu']);

        $this->assertDatabaseHas('menus', ['name' => 'New Menu']);
    }

    public function test_can_show_menu()
    {
        $menu = Menu::create(['name' => 'Menu Show', 'visible' => true]);

        $response = $this->actingAs($this->user)->getJson("/api/menus/{$menu->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Menu Show']);
    }

    public function test_can_update_menu()
    {
        $menu = Menu::create(['name' => 'Old Name', 'visible' => true]);

        $data = ['name' => 'New Name'];

        $response = $this->actingAs($this->user)->putJson("/api/menus/{$menu->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'name' => 'New Name']);
    }

    public function test_can_delete_menu()
    {
        $menu = Menu::create(['name' => 'To Delete', 'visible' => true]);

        $response = $this->actingAs($this->user)->deleteJson("/api/menus/{$menu->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }
}
