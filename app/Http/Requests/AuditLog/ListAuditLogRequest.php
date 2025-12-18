<?php

namespace App\Http\Requests\AuditLog;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ListAuditLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'   => 'email',
            'event'   => 'in:created,updated,deleted',
            'model'   => 'string',
            'modelId' => 'integer',
            'startAt' => [
                'required_with:endAt',
                'date_format:Y-m-d',
            ],
            'endAt'   => [
                'required_with:startAt',
                'date_format:Y-m-d',
                'after_or_equal:startAt',
                Rule::when(
                    $this->filled('startAt') &&
                    $this->isValidDate($this->startAt),
                    function () {
                        return 'before_or_equal:' . Carbon::createFromFormat('Y-m-d', $this->startAt)
                                ->addMonths(3)
                                ->format('Y-m-d');
                    }
                ),
            ]
        ];
    }

    private function isValidDate(string $date): bool
    {
        try {
            Carbon::createFromFormat('Y-m-d', $date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Informe um email válido.',
            'event.in' => 'O evento deve ser: created, updated ou deleted.',
            'startAt.date_format' => 'A data inicial deve estar no formato YYYY-MM-DD.',
            'startAt.required_with'=> 'A data inicial é obrigatória',
            'endAt.date_format' => 'A data final deve estar no formato YYYY-MM-DD.',
            'endAt.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
            'endAt.required_with'=> 'A data final é obrigatória',
            'endAt.before_or_equal'=> 'O intervalo máximo entre as datas é de 90 dias',
        ];
    }
}
