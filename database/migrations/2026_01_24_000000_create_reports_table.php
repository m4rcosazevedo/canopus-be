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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('format'); // pdf, csv, xlsx
            $table->string('template')->nullable(); // Blade view name for PDF
            $table->string('endpoint')->nullable(); // API endpoint to fetch data
            $table->boolean('authenticated')->default(false); // Whether the endpoint requires authentication
            $table->text('token')->nullable(); // Bearer token if provided manually
            $table->json('parameters')->nullable(); // Stores options, queryParams, fields, etc.
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->string('path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
