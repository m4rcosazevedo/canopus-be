<?php

namespace App\Http\Requests\UserAddress;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'number' => ['nullable', 'string', 'max:10'],
            'complement' => ['nullable', 'string', 'max:100'],
            'is_default' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_default.boolean' => 'O campo definir como principal é obrigatório.',
        ];
    }
}
