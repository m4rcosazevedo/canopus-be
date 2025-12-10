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
        Schema::create('plan_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('name');  // Nome do serviço extra (massagem, avaliação postural, aula particular, etc...)
            $table->text('description')->nullable();
            $table->decimal('extra_price', 10, 2)->default(0); // Preço adicional
            $table->integer('limit_per_month')->nullable(); // qtd mensal permitida
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_services');
    }
};
