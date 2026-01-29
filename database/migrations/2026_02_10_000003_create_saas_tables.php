<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adicionar status ao Tenant
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('status')->default('inactive')->after('domain'); // active, inactive, suspended, past_due
            $table->date('trial_ends_at')->nullable()->after('status');
        });

        // 2. Planos do SaaS (O que a empresa contrata)
        Schema::create('tenant_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: Basic Monthly, Pro Yearly
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('interval'); // monthly, yearly
            $table->integer('interval_count')->default(1); // 1 month, 1 year
            $table->json('features')->nullable(); // Limites: { "users": 5, "storage": "10gb" }
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Assinaturas (Vínculo Empresa -> Plano)
        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_plan_id')->constrained();
            $table->string('status'); // active, canceled, past_due, trialing
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable(); // Data de expiração (próxima cobrança)
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
        });

        // 4. Pagamentos (Histórico financeiro da Empresa)
        Schema::create('tenant_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('BRL');
            $table->string('payment_method'); // credit_card, pix, boleto
            $table->string('status'); // pending, paid, failed, refunded
            $table->string('transaction_id')->nullable(); // ID no Gateway (Stripe, MercadoPago, etc)
            $table->json('gateway_data')->nullable(); // Resposta completa do gateway
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_payments');
        Schema::dropIfExists('tenant_subscriptions');
        Schema::dropIfExists('tenant_plans');

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['status', 'trial_ends_at']);
        });
    }
};
