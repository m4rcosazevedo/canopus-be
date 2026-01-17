<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sort' => 'string',
            'order' => 'string'
        ];
    }
}
