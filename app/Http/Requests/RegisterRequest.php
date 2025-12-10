<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    const STUDENT_ID = 3;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cellphone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        $data['user_type_id'] = self::STUDENT_ID;
        return $data;
    }
}
