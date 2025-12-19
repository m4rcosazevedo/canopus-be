<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT');

        return [
            'zip_code' => 'required|string|size:8',
            'city_id' => $isUpdate ? 'required|exists:cities,id' : 'nullable|exists:cities,id',
            'street_name' => $isUpdate ? 'required|string|max:150' : 'nullable|string|max:150',
            'street_type' => 'required|string|max:20',
            'district' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'zip_code.size' => 'O CEP deve conter exatamente 8 dígitos numéricos.',
            'city_id.exists' => 'A cidade selecionada não existe no sistema.',
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
