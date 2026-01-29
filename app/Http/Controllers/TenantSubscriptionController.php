<?php

namespace App\Http\Controllers;

use App\Models\TenantPlan;
use App\Services\TenantSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantSubscriptionController extends Controller
{
    protected TenantSubscriptionService $service;

    public function __construct(TenantSubscriptionService $service)
    {
        $this->service = $service;
    }

    /**
     * Lista os planos disponíveis para assinatura.
     */
    public function index()
    {
        return response()->json(TenantPlan::where('is_active', true)->get());
    }

    /**
     * Realiza a assinatura de um plano.
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:tenant_plans,id',
            'payment_token' => 'required|string',
            'gateway' => 'in:stripe,mercadopago'
        ]);

        $user = Auth::user();
        $tenant = $user->tenant; // Assume que o usuário já tem um tenant vinculado

        if (!$tenant) {
            return response()->json(['message' => 'Usuário não possui uma empresa vinculada.'], 400);
        }

        $plan = TenantPlan::find($request->plan_id);
        $gateway = $request->input('gateway', 'stripe');

        try {
            $payment = $this->service->subscribe(
                $tenant,
                $plan,
                $request->payment_token,
                $gateway
            );

            if ($payment->status === 'paid') {
                return response()->json([
                    'message' => 'Assinatura realizada com sucesso!',
                    'status' => 'active',
                    'payment_id' => $payment->id
                ]);
            }

            return response()->json([
                'message' => 'Pagamento falhou ou está pendente.',
                'status' => $payment->status,
                'error' => $payment->gateway_data['error'] ?? null
            ], 402);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar assinatura.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
