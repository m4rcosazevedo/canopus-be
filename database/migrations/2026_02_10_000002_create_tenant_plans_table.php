<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenant_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: Basic Monthly, Pro Yearly
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('interval'); // monthly, yearly
            $table->integer('interval_count')->default(1); // 1 month, 1 year
            $table->text('features_available')->nullable(); // o que o plano oferece: Gestão de Alunos;Controle financeiro básico...
            $table->json('features')->nullable(); // Limites: { "users": 5, "storage": "10gb" }
            $table->boolean('is_active')->default(true);
            $table->boolean('popular')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_plans');
    }
};
