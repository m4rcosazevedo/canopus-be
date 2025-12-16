<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected Builder $builder;
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Retorna apenas os filtros permitidos
     */
    protected function filters(): array
    {
        return $this->request->query();
    }

    /**
     * Aplica os filtros na query
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->filters() as $filter => $value) {
            if ($this->shouldApplyFilter($filter, $value)) {
                $this->applyFilter($filter, $value);
            }
        }

        return $this->builder;
    }

    /**
     * Verifica se o filtro pode ser aplicado
     */
    protected function shouldApplyFilter(string $filter, mixed $value): bool
    {
        if (!method_exists($this, $filter)) {
            return false;
        }

        if ($value === null || $value === '') {
            return false;
        }

        if (is_array($value) && empty(array_filter($value))) {
            return false;
        }

        return true;
    }

    /**
     * Executa o filtro
     */
    protected function applyFilter(string $filter, mixed $value): void
    {
        $this->{$filter}($value);
    }
}
