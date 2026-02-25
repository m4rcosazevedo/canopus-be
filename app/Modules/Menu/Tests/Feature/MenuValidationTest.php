<?php

namespace App\Modules\Menu\Tests\Feature;

use App\Models\User;
use App\Modules\Menu\Models\Menu;
use App\Modules\UserType\Model\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userType = UserType::create(['name' => 'Admin', 'description' => 'Admin User', 'visible' => true]);
        $this->user = User::factory()->create(['user_type_id' => $this->userType->id]);
    }

    public function test_menu_without_route_must_have_child_with_route()
    {
        // Menu pai sem rota
        $parentMenu = Menu::create(['name' => 'Parent Menu', 'visible' => true]);

        // Menu filho com rota
        $childMenu = Menu::create([
            'name' => 'Child Menu',
            'route' => '/child-route',
            'parent_id' => $parentMenu->id,
            'visible' => true
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/menus/available');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Parent Menu'])
            ->assertJsonFragment(['name' => 'Child Menu']);
    }

    public function test_menu_without_route_and_without_child_with_route_is_hidden()
    {
        // Menu pai sem rota
        $parentMenu = Menu::create(['name' => 'Parent Menu', 'visible' => true]);

        // Menu filho sem rota
        $childMenu = Menu::create([
            'name' => 'Child Menu',
            'parent_id' => $parentMenu->id,
            'visible' => true
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/menus/available');

        $response->assertStatus(200)
            ->assertJsonMissing(['name' => 'Parent Menu']);
    }
}
