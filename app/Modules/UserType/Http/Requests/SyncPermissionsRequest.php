<?php

namespace App\Modules\UserType\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncPermissionsRequest extends FormRequest
{
    public mixed $permissions;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'permissions.*.exists' => 'A permissão selecionada não é inválido.',
        ];
    }
}
