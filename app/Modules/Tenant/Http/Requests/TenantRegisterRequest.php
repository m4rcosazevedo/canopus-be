<?php

namespace App\Modules\Tenant\Http\Requests;

use App\Helpers\DocumentHelper;
use App\Modules\DocumentType\Models\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantRegisterRequest extends FormRequest
{
    public function rules(): array
    {
//        $tenantId = $this->tenant_id;
        // ou auth()->user()->tenant_id
        // ou $this->route('tenant')->id

        return [
            'name' => 'required|string|max:255|regex:/^[A-Za-zÀ-ÿ]/',
            'email' => 'required|string|email|max:255|unique:users',
//            'email' => [
//                'required',
//                'string',
//                'email',
//                'max:255',
//                Rule::unique('users')
//                    ->where(fn ($query) =>
//                        $query->where('tenant_id', $tenantId)
//                    )
//                    ->ignore($this->route('user')->id),
//            ],
            'cellphone' => 'required|string|max:20',
            'document' => ['required', 'string'],
            'number' => ['required'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[0-9]/',              // pelo menos 1 número
                'regex:/[a-z]/',              // pelo menos 1 letra minúscula
                'regex:/[A-Z]/',              // pelo menos 1 letra maiúscula
                'regex:/[^a-zA-Z0-9]/',       // pelo menos 1 caractere especial
            ],
//            'document_type_id' => ['required'],

            'companyName' => 'required|max:200',
            'domain' => [
                'string',
                'max:200',
                'regex:/^(https?:\/\/)?(www\.)?([a-z0-9-]+\.)+[a-z]{2,}$/i',
                Rule::unique('tenants', 'domain')
//                    ->ignore($tenantId)
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->cellphone) {
            $this->merge([
                'cellphone' => preg_replace('/[^0-9]/', '', $this->cellphone),
            ]);
        }

        if (!$this->has('document')) {
            return;
        }

        $document = strtoupper(
            preg_replace('/[^A-Z0-9]/i', '', $this->input('document'))
        );

        if (DocumentHelper::isCpfValid($document)) {
            $this->merge([
                'document_type_id' => DocumentType::CPF,
                'number' => $document,
            ]);
        } elseif (DocumentHelper::isCnpjValid($document)) {
            $this->merge([
                'document_type_id' => DocumentType::CNPJ,
                'number' => $document,
            ]);
        }

        $this->request->remove('document');
    }

    public function messages(): array
    {
        return [
            'number.required' => 'O Documento é inválido',
            'password.required' => 'A senha é obrigatória.',
            'password.string' => 'A senha deve ser uma string.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos 1 número, 1 letra minúscula, 1 letra maiúscula e 1 caractere especial.',
        ];
    }
}
