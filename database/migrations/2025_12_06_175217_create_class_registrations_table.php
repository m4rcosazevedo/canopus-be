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
        Schema::create('class_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_plan_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
            $table->timestamp('class_date');

            $table->enum('status', ['scheduled', 'attended', 'missed', 'canceled'])
                ->default('scheduled'); // Status de presença

            // Um aluno não pode se registrar 2x na mesma aula na mesma data
            $table->unique(['user_id', 'class_id', 'class_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_registrations');
    }
};
