<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name'      => 'sometimes|required|string|max:255|regex:/^[A-Za-zÀ-ÿ]/',
            'email'     => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
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
