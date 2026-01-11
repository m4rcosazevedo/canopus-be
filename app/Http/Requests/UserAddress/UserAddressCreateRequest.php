<?php

namespace App\Http\Requests\UserAddress;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'zip_code' => ['required', 'string', 'size:8'],
            'street_type' => ['nullable', 'string', 'max:20'],
            'street_name' => ['required', 'string', 'max:150'],
            'district' => ['nullable', 'string', 'max:100'],
            'city_id' => ['required', 'exists:cities,id'],

            'number' => ['nullable', 'string', 'max:10'],
            'complement' => ['nullable', 'string', 'max:100'],
            'is_default' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'zip_code.size' => trans('address.errors.zip_code_size'),
            'city_id.exists' => trans('address.errors.city_no_record'),
            'is_default.boolean' => 'O campo definir como principal é obrigatório.',
        ];
    }
}
