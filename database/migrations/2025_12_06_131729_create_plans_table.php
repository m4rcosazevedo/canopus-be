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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do plano (Mensal, Semestral, 10 aulas)
            $table->text('description')->nullable(); // Descrição detalhada
            $table->enum('type', ['monthly', 'package', 'unlimited'])->default('monthly'); // Tipo do plano: mensal, pacote de aulas ou ilimitado
            $table->integer('duration_in_days')->nullable(); // Validade do plano (ex: 30 dias, 90 dias...)
            $table->integer('max_classes_per_week')->nullable(); // Aulas/semana permitidas
            $table->integer('total_class_credits')->nullable(); // Créditos totais (para pacotes, ex: 10 aulas)
            $table->decimal('price', 10, 2); // Valor do plano
            $table->boolean('allow_makeup_classes')->default(false); // Permite reposição?
            $table->integer('max_makeup_per_month')->nullable(); // Limite de reposições
            $table->boolean('can_freeze')->default(false); // Plano permite congelamento?
            $table->integer('max_freeze_days')->nullable(); // Máximo de dias congelados
            $table->boolean('status')->default(true); //Disponível no sistema
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
