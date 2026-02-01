<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sandbox_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('external_reference')->nullable()->index();
            $table->integer('amount'); // Centavos
            $table->string('currency')->default('BRL');
            $table->string('payment_method');
            $table->string('status');

            // Payer info
            $table->string('payer_email')->nullable();
            $table->string('payer_document')->nullable();

            // Card info
            $table->string('card_last_four')->nullable();

            // Pix info
            $table->text('pix_qr_code')->nullable();
            $table->string('pix_qr_code_url')->nullable();

            // Boleto info
            $table->string('boleto_url')->nullable();
            $table->string('boleto_barcode')->nullable();

            // Webhook
            $table->string('webhook_url')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sandbox_transactions');
    }
};
