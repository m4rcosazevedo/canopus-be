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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('topics')->cascadeOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('topics')->cascadeOnDelete();

            $table->enum('source_type', [
                'enem', 'fuvest', 'unicamp', 'unesp', 'famerp', 'santa_casa',
                'usp', 'unifesp', 'simulado', 'outros'
            ])->default('outros');
            $table->integer('source_year')->nullable();     // ano da prova
            $table->string('source_edition')->nullable();   // ex: "1º dia", "2º dia"
            $table->string('source_code')->nullable();      // código único externo, se houver

            $table->longText('statement');                 // Enunciado completo
            $table->text('context')->nullable();           // Texto de apoio / contexto
            $table->string('image_url')->nullable();       // Imagem da questão
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->enum('type', ['multiple_choice', 'true_false', 'essay'])->default('multiple_choice');
            $table->text('explanation')->nullable();       // Resolução comentada
            $table->text('tags')->nullable();              // JSON de tags para busca
            $table->boolean('active')->default(true);

            $table->index(['discipline_id', 'difficulty']);
            $table->index(['source_type', 'source_year']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
