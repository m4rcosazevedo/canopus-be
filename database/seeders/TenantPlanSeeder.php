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
            'features_available' => 'Gestão de alunos;Controle financeiro básico;Até 100 alunos ativos;Suporte via e-mail',
        ]);

        TenantPlan::create([
            'name' => 'Anual Pro',
            'slug' => 'yearly-pro',
            'description' => 'Plano anual com desconto e recursos ilimitados.',
            'price' => 999.00, // ~83.25/mês
            'interval' => 'yearly',
            'interval_count' => 1,
            'features' => ['users' => 999, 'students' => 9999],
            'features_available' => 'Tudo do plano Básico;Gestão ilimitada;Treinos personalizados app;Controle de acesso (catraca);Suporte prioritário (WhatsApp)',
            'popular' => true
        ]);
    }
}
