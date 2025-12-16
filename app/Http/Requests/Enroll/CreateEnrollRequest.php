<?php

namespace App\Http\Requests\Enroll;

use App\Models\User;
use App\Rules\StudentHasNoActivePlan;
use Illuminate\Foundation\Http\FormRequest;

class CreateEnrollRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                new StudentHasNoActivePlan()
            ],
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date|after_or_equal:today'
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
            $validator->errors()->add('user_id', trans('enroll.errors.invalid_user_type'));
        }
    }
}
