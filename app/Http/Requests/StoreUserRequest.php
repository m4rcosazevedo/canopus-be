<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cellphone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'type'       => 'required|integer|exists:user_types,id'
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        $data['user_type_id'] = $data['type'];
        unset($data['type']);
        return $data;
    }
}
