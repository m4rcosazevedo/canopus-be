<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class AddressSearchByZipCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'zip_code' => 'required|string|size:8'
        ];
    }

    public function messages(): array
    {
        return [
            'zip_code.size' => trans('address.errors.zip_code_size'),
            'city_id.exists' => trans('address.errors.city_no_record'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->zip_code) {
            $this->merge([
                'zip_code' => preg_replace('/[^0-9]/', '', $this->zip_code),
            ]);
        }
    }
}
