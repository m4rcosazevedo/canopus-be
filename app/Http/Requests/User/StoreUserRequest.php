<?php

namespace App\Http\Requests\User;

use App\Modules\DocumentType\Models\DocumentType;
use App\Rules\Cnpj;
use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|regex:/^[A-Za-zÀ-ÿ]/',
            'email' => 'required|string|email|max:255|unique:users',
            'cellphone' => 'required|string|max:20',
            'type'       => 'required|integer|exists:user_types,id',

            'document_type_id' => ['required', 'exists:document_types,id'],
            'number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('user_documents', 'number'), // Garante que o número é único
                function ($attribute, $value, $fail) {
                    if ($this->document_type_id === DocumentType::CPF) {
                        (new Cpf())->validate($attribute, $value, $fail);
                    }

                    if ($this->document_type_id === DocumentType::CNPJ) {
                        (new Cnpj)->validate($attribute, $value, $fail);
                    }
                }
            ],
            'issuer' => ['nullable', 'string', 'max:50'],
            'state_id' => 'nullable|exists:states,id',
            'issued_at' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'O nome deve começar com uma letra.',
            'state_id.exists' => 'O estado selecionado é inválido.',
            'document_type_id.exists' => 'O tipo de documento selecionado é inválido.',
            'number.unique' => 'Este número de documento já está cadastrado no sistema.',
            'issued_at.before_or_equal' => 'A data de emissão não pode ser futura.',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        $data['user_type_id'] = $data['type'];
        unset($data['type']);
        return $data;
    }

    protected function prepareForValidation(): void
    {
        if ($this->cellphone) {
            $this->merge([
                'cellphone' => preg_replace('/[^0-9]/', '', $this->cellphone),
            ]);
        }
    }
}
