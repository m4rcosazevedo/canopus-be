<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::create([
            'name' => 'Academia Exemplo',
            'slug' => 'academia-exemplo',
            'domain' => 'exemplo.canopus.local',
        ]);

        $adminType = UserType::firstOrCreate(['name' => 'Admin']);

        User::create([
            'name' => 'Admin Tenant',
            'email' => 'admin@exemplo.com',
            'password' => 'password',
            'tenant_id' => $tenant->id,
            'user_type_id' => $adminType->id,
        ]);
    }
}
