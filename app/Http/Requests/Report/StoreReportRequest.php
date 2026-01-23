<?php

declare(strict_types=1);

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string'],
            'template' => ['nullable', 'string'],
            'endpoint' => ['required', 'url'],
            'authenticated' => ['boolean'],
            'token' => ['nullable', 'string'],
            'options' => ['required', 'array'],
            'options.type' => ['required', 'in:pdf,csv,xlsx'],
            'options.format' => ['nullable', 'in:array,object'],
            'options.contentKey' => ['nullable', 'string'],
            'options.paginate' => ['nullable', 'array'],
            'options.paginate.queryFieldKey' => ['required_with:options.paginate', 'string'],
            'options.paginate.currentPage' => ['required_with:options.paginate', 'string'],
            'options.paginate.lastPage' => ['required_with:options.paginate', 'string'],
            'options.queryParams' => ['nullable', 'array'],
            'options.fields' => ['required', 'array'],
            'options.title' => ['nullable', 'string'],
            'options.queryDisplay' => ['nullable', 'array'],
        ];
    }
}
