<?php

namespace App\Modules\Plan\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'description' => ['nullable', 'string'],

            'price' => [
                'required',
                'numeric',
                'min:0'
            ],

            'period' => [
                'required',
                Rule::in(['monthly', 'yearly']),
            ],

            'active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do plano é obrigatório.',
            'name.max' => 'O nome do plano deve ter no máximo 255 caracteres.',

            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um valor numérico.',
            'price.min' => 'O preço não pode ser negativo.',

            'period.required' => 'O período é obrigatório.',
            'period.in' => 'O período deve ser monthly ou yearly.',

            'active.boolean' => 'O campo active deve ser verdadeiro ou falso.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('active')) {
            $this->merge([
                'active' => filter_var($this->active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

}
