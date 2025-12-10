<?php

namespace App\Http\Requests\Class;

use App\Enum\ClassStatusEnum;
use App\Enum\ClassWeekdayEnum;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateClassRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // 'plan_id' não é incluído aqui porque não pode ser alterado
            'user_id'    => 'required|exists:users,id',
            'weekday'    => ['required', new Enum(ClassWeekdayEnum::class)],
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'capacity'   => 'required|integer|min:1|max:1000',
            'room'       => 'sometimes|string|max:255',
            'status'     => ['required', new Enum(ClassStatusEnum::class)],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateUserType($validator);
            $this->validateScheduleConflict($validator);
        });
    }

    private function validateUserType($validator): void
    {
        $user = User::find($this->user_id);

        if (!$user || !in_array($user->userType->name, ['instructor', 'administrator'])) {
            $validator->errors()->add('user_id', 'The user must have the type "instructor" or "administrator".');
        }
    }

    private function validateScheduleConflict($validator): void
    {
        if (!$this->user_id || !$this->weekday || !$this->start_time || !$this->end_time) {
            return;
        }

        $planId = $this->route('class')->plan_id;
        $classId = $this->route('class')->id;

        $conflict = ClassModel::where('plan_id', $planId)
            ->where('user_id', $this->user_id)
            ->where('weekday', $this->weekday)
            ->where(function ($query) {
                $query->where('start_time', '<', $this->end_time)
                    ->where('end_time', '>', $this->start_time);
            })
            ->where('id', '<>', $classId)
            ->exists();

        if ($conflict) {
            $validator->errors()->add(
                'start_time',
                'There is already a class for this plan, user, and weekday that conflicts with the provided time.'
            );
        }
    }
}
