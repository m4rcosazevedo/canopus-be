<?php

use Illuminate\Support\Facades\Route;
use App\Modules\PaymentsSandbox\Controllers\TransactionController;
use App\Modules\PaymentsSandbox\Controllers\WebhookController;

Route::post('/transactions', [TransactionController::class, 'store']);
Route::get('/transactions/{id}', [TransactionController::class, 'show']);
Route::post('/transactions/{id}/refund', [TransactionController::class, 'refund']);

// Rota para simular o recebimento de webhooks (opcional, para testes manuais)
Route::post('/webhooks/trigger/{transactionId}', [WebhookController::class, 'trigger']);
