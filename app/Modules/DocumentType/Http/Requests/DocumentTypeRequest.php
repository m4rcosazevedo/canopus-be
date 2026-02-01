<?php

namespace App\Modules\DocumentType\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentTypeRequest extends FormRequest
{
    public function rules(): array
    {
        $documentTypeId = $this->route('document_type');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('document_types', 'name')->ignore($documentTypeId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:200',
            ],

        ];
    }
}
