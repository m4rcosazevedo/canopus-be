<?php

namespace App\Modules\Area\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da área é obrigatório.',
            'name.max' => 'O nome da área deve ter no máximo 255 caracteres.',
        ];
    }

}
