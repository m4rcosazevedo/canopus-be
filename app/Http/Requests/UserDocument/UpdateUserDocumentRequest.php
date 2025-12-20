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
            'state' => ['nullable', 'string', 'size:2'],
            'issued_at' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'issued_at.before_or_equal' => 'A data de emissão não pode ser futura.',
        ];
    }
}
