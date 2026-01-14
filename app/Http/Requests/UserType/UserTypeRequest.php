<?php

namespace App\Http\Requests\UserType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserTypeRequest extends FormRequest
{
    public function rules(): array
    {
        $userTypeId = $this->route('user_type');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('user_types', 'name')->ignore($userTypeId),
            ],
            'description' => [
                'required',
                'string',
                'max:200',
            ],

        ];
    }
}
