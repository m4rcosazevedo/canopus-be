<?php

namespace App\Http\Requests\TenantPlan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
//        $planId = $this->route('tenant_plan');

        return [
            'name' => ['required', 'string', 'max:255'],

//            'slug' => [
//                'required',
//                'string',
//                'max:255',
//                Rule::unique('tenant_plans', 'slug')->ignore($planId),
//            ],

            'description' => ['nullable', 'string'],

            'price' => ['required', 'numeric', 'min:0'],

            'interval' => [
                'required',
                'string',
                Rule::in(['monthly', 'yearly']),
            ],

            'interval_count' => ['required', 'integer', 'min:1'],

            'features_available' => ['nullable', 'array'],
            'features_available.*' => ['string', 'filled', 'max:255'],

            'features' => ['nullable', 'array'],
            // ex: { "users": 5, "storage": "10gb" }

            'is_active' => ['boolean'],

            'popular' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do plano é obrigatório.',
            'slug.required' => 'O slug é obrigatório.',
            'slug.unique' => 'Este slug já está em uso.',
            'price.required' => 'O preço do plano é obrigatório.',
            'price.numeric' => 'O preço deve ser um valor numérico.',
            'interval.required' => 'O intervalo é obrigatório.',
            'interval.in' => 'O intervalo deve ser mensal ou anual.',
            'interval_count.min' => 'O intervalo deve ser no mínimo 1.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'popular' => $this->boolean('popular'),
        ]);
    }
}
