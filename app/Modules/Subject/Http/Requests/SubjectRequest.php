<?php

namespace App\Modules\Subject\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subjectId = $this->route('subject');


        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')
                    ->where(fn ($query) =>
                    $query->where('area_id', $this->area_id)
                    )
                    ->ignore($subjectId),
            ],

            'area_id' => [
                'required',
                'integer',
                'exists:areas,id',
            ],

            'color_hex' => [
                'required',
                'string',
                'size:7',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da disciplina é obrigatório.',
            'name.unique' => 'Já existe uma disciplina com esse nome nesta área.',

            'area_id.required' => 'A área é obrigatória.',
            'area_id.exists' => 'A área selecionada é inválida.',

            'color_hex.required' => 'A cor é obrigatória.',
            'color_hex.size' => 'A cor deve conter 7 caracteres.',
            'color_hex.regex' => 'A cor deve estar no formato hexadecimal válido. Ex: #FF0000',
        ];
    }

}
