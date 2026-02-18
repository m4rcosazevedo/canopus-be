<?php

namespace App\Modules\City\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cityId = $this->route('city')?->id;

        return [
            'state_id' => 'required|exists:states,id',
            'name' => 'required|string|max:100',
            'ibge_code' => [
                'required',
                'integer',
                Rule::unique('cities', 'ibge_code')->ignore($cityId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'state_id.exists' => 'O estado selecionado é inválido.',
            'ibge_code.unique' => 'Este código IBGE já está atribuído a outra cidade.',
        ];
    }
}
