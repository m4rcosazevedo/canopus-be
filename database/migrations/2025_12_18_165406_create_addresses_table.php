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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->char('zip_code', 8)->index(); // CEP (apenas números)
            $table->string('street_type', 20)->nullable(); // Ex: Rua, Av, Praça
            $table->string('street_name', 150);
            $table->string('district', 100)->nullable(); // Bairro

            // Relacionamento com cidade
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
