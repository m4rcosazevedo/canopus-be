<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sandbox_idempotencies', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('response_body');
            $table->integer('response_code');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sandbox_idempotencies');
    }
};
