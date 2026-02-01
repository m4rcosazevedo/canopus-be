<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Validar se o payload tem o que precisamos
        $data = $request->validate([
            'data.external_reference' => 'required',
            'data.status' => 'required|string',
            'data.id' => 'required',
        ]);

        $externalReference = $data['data']['external_reference'];
        $status = $data['data']['status'];
        $transactionId = $data['data']['id'];

        Log::info("Webhook recebido para Pedido #{$externalReference}. Status: {$status}");

        // 2. Aqui você buscaria o pedido no seu banco
        // Exemplo: $order = Order::find($externalReference);

        // if (!$order) {
        //     return response()->json(['message' => 'Order not found'], 404);
        // }

        // 3. Atualizar o status do pedido
        // if ($status === 'paid') {
        //     $order->update(['status' => 'paid', 'paid_at' => now()]);
        //     // Liberar acesso, enviar email, etc.
        // } elseif ($status === 'failed') {
        //     $order->update(['status' => 'failed']);
        // }

        return response()->json(['message' => 'Webhook processed']);
    }
}
