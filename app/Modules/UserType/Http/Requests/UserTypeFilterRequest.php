<?php

namespace App\Modules\UserType\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserTypeFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:100'],
            'visible' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'visible' => filter_var($this->visible, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        ]);
    }
}
