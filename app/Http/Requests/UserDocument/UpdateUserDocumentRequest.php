<?php

namespace App\Http\Requests\UserDocument;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'issuer' => ['nullable', 'string', 'max:50'],
            'state_id' => 'required|exists:states,id',
            'issued_at' => ['nullable', 'date', 'before_or_equal:now'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'state_id.exists' => 'O estado selecionado é inválido.',
            'issued_at.before_or_equal' => 'A data de emissão não pode ser futura.',
        ];
    }
}
