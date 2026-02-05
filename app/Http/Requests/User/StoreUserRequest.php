<?php

namespace App\Http\Requests\User;

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
            'name' => 'required|string|max:255|regex:/^[A-Za-zÀ-ÿ]/',
            'email' => 'required|string|email|max:255|unique:users',
            'cellphone' => 'required|string|max:20',
            'type'       => 'required|integer|exists:user_types,id'
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'O nome deve começar com uma letra.'
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
