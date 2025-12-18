<?php

namespace App\Http\Requests\AuditLog;

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
            'startAt' => 'required_with:endAt|date_format:Y-m-d',
            'endAt'   => 'required_with:startAt|date_format:Y-m-d|after_or_equal:startAt',
            'model'   => 'string',
            'modelId' => 'integer',
        ];
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
        ];
    }
}
