<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserTypeSeeder::class,
            DocumentTypeSeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            TenantSeeder::class,
            TenantPlanSeeder::class,
        ]);

//        User::factory()->create([
//            'name' => 'Marcos Azevedo',
//            'email' => 'm4rcos.azevedo@gmail.com',
//            'user_type_id' => 1,
//            'password' => bcrypt('senha_segura'),
//            'cellphone' => '77981378010'
//        ]);
    }
}
