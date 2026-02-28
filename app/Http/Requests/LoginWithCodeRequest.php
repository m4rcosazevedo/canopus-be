<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginWithCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'code' => ['required', 'string', 'size:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'O email é inválido ou não está cadastrado.',
        ];
    }
}
