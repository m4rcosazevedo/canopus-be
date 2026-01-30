<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = $this->route('tenant')?->id;

        return [
            'name' => 'required|max:200',
            'domain' => [
                'string',
                'max:200',
                'regex:/^(https?:\/\/)?(www\.)?([a-z0-9-]+\.)+[a-z]{2,}$/i',
                Rule::unique('tenants', 'domain')->ignore($tenantId)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'domain.max' => 'O domínio deve ter no máximo 200 caracteres',
            'domain.regex' => 'O domínio não é um endereço válido',
            'unique.regex' => 'Este domínio já está em uso.'
        ];
    }
}
