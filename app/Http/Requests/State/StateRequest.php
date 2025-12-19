<?php

namespace App\Http\Requests\State;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $stateId = $this->route('state')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('states', 'name')->ignore($stateId),
            ],
            'abbr' => [
                'required',
                'string',
                'size:2',
                Rule::unique('states', 'abbr')->ignore($stateId),
            ],
            'ibge_code' => [
                'required',
                'integer',
                Rule::unique('states', 'ibge_code')->ignore($stateId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'abbr.size' => 'A sigla do estado deve ter exatamente 2 caracteres.',
            'name.unique' => 'Este nome de estado já está cadastrado.',
            'ibge_code.unique' => 'Este código IBGE já pertence a outro estado.',
        ];
    }
}
