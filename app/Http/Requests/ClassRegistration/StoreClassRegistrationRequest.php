<?php

namespace App\Http\Requests\ClassRegistration;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreClassRegistrationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'student_plan_id'    => 'required|exists:student_plans,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateUserType($validator);
        });
    }

    private function validateUserType($validator): void
    {
        $user = User::find($this->user_id);

        if (!$user || !in_array($user->userType->name, ['student'])) {
            $validator->errors()->add('user_id', trans('class_registration.errors.invalid_user_type'));
        }
    }
}
