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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('weekday'); // ex: monday, tuesday...
            $table->time('start_time'); // 09:00
            $table->time('end_time');   // 10:00
            $table->integer('capacity')->default(10);
            $table->string('room')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();

            // indices
            $table->index(['weekday', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
