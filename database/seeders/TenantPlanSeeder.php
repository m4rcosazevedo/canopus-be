<?php

namespace Database\Seeders;

use App\Models\TenantPlan;
use Illuminate\Database\Seeder;

class TenantPlanSeeder extends Seeder
{
    public function run(): void
    {
        TenantPlan::create([
            'name' => 'Mensal Básico',
            'slug' => 'monthly-basic',
            'description' => 'Plano mensal para pequenas academias.',
            'price' => 99.90,
            'interval' => 'monthly',
            'interval_count' => 1,
            'features' => ['users' => 5, 'students' => 100],
        ]);

        TenantPlan::create([
            'name' => 'Anual Pro',
            'slug' => 'yearly-pro',
            'description' => 'Plano anual com desconto e recursos ilimitados.',
            'price' => 999.00, // ~83.25/mês
            'interval' => 'yearly',
            'interval_count' => 1,
            'features' => ['users' => 999, 'students' => 9999],
        ]);
    }
}
