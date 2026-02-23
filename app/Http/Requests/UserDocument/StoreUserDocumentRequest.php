<?php

namespace App\Http\Requests\UserDocument;

use App\Modules\DocumentType\Models\DocumentType;
use App\Rules\Cnpj;
use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'state_id.exists' => 'O estado selecionado é inválido.',
            'document_type_id.exists' => 'O tipo de documento selecionado é inválido.',
            'number.unique' => 'Este número de documento já está cadastrado no sistema.',
            'issued_at.before_or_equal' => 'A data de emissão não pode ser futura.',
        ];
    }
}
