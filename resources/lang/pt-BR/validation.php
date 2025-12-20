<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'required' => 'O campo :attribute é obrigatório.',
    'string' => 'O campo :attribute deve ser um texto.',
    'email' => 'O campo :attribute deve ser um e-mail válido.',
    'unique' => 'Este :attribute já está em uso.',
    'min' => [
        'numeric' => 'O campo :attribute deve ter pelo menos :min.',
        'string' => 'O campo :attribute deve ter no mínimo :min caracteres.',
    ],
    'max' => [
        'numeric' => 'O campo :attribute deve ser no máximo :max.',
        'string' => 'O campo :attribute deve ter no máximo :max caracteres.',
    ],
    'regex' => 'O formato do campo :attribute é inválido.',
    'confirmed' => 'A confirmação de :attribute não confere.',
    'enum' => 'O valor selecionado para :attribute é inválido.',
    'exists' => 'O :attribute informado é inválido.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'date_format' => 'O campo :attribute deve estar no formato :format.',
    'after' => 'O campo :attribute deve ser posterior ao campo :date.',
    'date' => 'O campo :attribute deve ser uma data válida.',
    'after_or_equal' => 'O campo :attribute deve ser uma data igual ou posterior a :date.',
    'numeric' => 'O campo :attribute deve ser um número.',
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name'       => 'nome',
        'email'      => 'e-mail',
        'cellphone'  => 'celular',
        'type'       => 'tipo de usuário',
        'password'   => 'senha',
        'plan_id'    => 'plano',
        'user_id'    => 'usuário',
        'weekday'    => 'dia da semana',
        'start_time' => 'horário inicial',
        'end_time'   => 'horário final',
        'capacity'   => 'capacidade',
        'room'       => 'sala',
        'status'     => 'status',
        'start_date' => 'data de início',
        'student_plan_id' => 'plano do aluno',
        'description' => 'descrição',
        'duration_in_days' => 'duração em dias',
        'max_classes_per_week' => 'máximo de aulas por semana',
        'total_class_credits' => 'total de créditos de aulas',
        'price' => 'preço',
        'allow_makeup_classes' => 'permitir reposições',
        'max_makeup_per_month' => 'máximo de reposições por mês',
        'can_freeze' => 'permitir congelamento',
        'max_freeze_days' => 'máximo de dias de congelamento',
        'zip_code'      => 'CEP',
        'city_id'       => 'cidade',
        'street_name'   => 'logradouro',
        'street_type'   => 'tipo de logradouro',
        'district'      => 'bairro',
        'ibge_code'     => 'código do IBGE',
        'abbr'          => 'sigla do estado',
        'issued_at'     => 'data de emissão',
    ],
];
