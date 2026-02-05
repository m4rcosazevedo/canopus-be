<?php

namespace App\Http\Requests\Plan;

use App\Enum\PlanTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdatePlanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'                  => 'required|string|max:255',
            'description'           => 'string',
            'type'                  => ['required', new Enum(PlanTypeEnum::class)],
            'duration_in_days'      => 'integer|min:1|max:365',
            'max_classes_per_week'  => 'integer|min:1|max:5',
            'total_class_credits'   => 'integer|min:1|max:250',
            'price'                 => 'required|numeric|min:0|max:999999999',
            'allow_makeup_classes'  => 'boolean',
            'max_makeup_per_month'  => 'integer|min:0|max:5',
            'can_freeze'            => 'boolean',
            'max_freeze_days'       => 'integer|min:1|max:30',
            'status'                => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'price.max' => 'O valor do plano é muito alto.',
            'duration_in_days.max' => 'A duração máxima do plano é de 365 dias.',
            'max_classes_per_week.max' => 'O máximo de aulas por semana é 5.',
        ];
    }
}
