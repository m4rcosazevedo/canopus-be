<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

abstract class SortableQueryFilter extends QueryFilter
{
    protected array $sortable = [];

    protected string $sortColumn = 'id';
    protected string $sortOrder  = 'desc';

    protected array $allowedOrders = ['asc', 'desc'];

    public function sort(?string $sort): void
    {
        if (!$sort || !isset($this->sortable[$sort])) {
            return;
        }

        $this->sortColumn = $this->sortable[$sort];
    }

    public function order(?string $order): void
    {
        $order = strtolower((string) $order);

        if (in_array($order, $this->allowedOrders, true)) {
            $this->sortOrder = $order;
        }
    }

    public function apply(Builder $builder): Builder
    {
        parent::apply($builder);

        return $this->applySorting($this->builder);
    }

    protected function applySorting(Builder $builder): Builder
    {
        if (Str::contains($this->sortColumn, '.')) {
            return $this->applyRelationSortingWithSubquery($builder);
        }

        return $builder->orderBy(
            $builder->getModel()->getTable() . '.' . $this->sortColumn,
            $this->sortOrder
        );
    }

    protected function applyRelationSortingWithSubquery(Builder $builder): Builder
    {
        [$relationName, $column] = explode('.', $this->sortColumn);

        $relation = $builder->getModel()->{$relationName}();

        $related = $relation->getRelated();
        $relatedTable = $related->getTable();
        $parentTable  = $builder->getModel()->getTable();

        /*
         |--------------------------------------------------------------------------
         | BelongsTo
         |--------------------------------------------------------------------------
         */
        if ($relation instanceof BelongsTo) {
            return $builder->orderBy(
                $related->select($column)
                    ->whereColumn(
                        "{$relatedTable}.{$relation->getOwnerKeyName()}",
                        "{$parentTable}.{$relation->getForeignKeyName()}"
                    )
                    ->limit(1),
                $this->sortOrder
            );
        }

        /*
         |--------------------------------------------------------------------------
         | HasOne
         |--------------------------------------------------------------------------
         */
        if ($relation instanceof HasOne) {
            return $builder->orderBy(
                $related->select($column)
                    ->whereColumn(
                        "{$relatedTable}.{$relation->getForeignKeyName()}",
                        "{$parentTable}.{$relation->getLocalKeyName()}"
                    )
                    ->limit(1),
                $this->sortOrder
            );
        }

        return $builder;
    }
}
