<?php

namespace App\Queries;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Builder;

class PlanAvailableQuery
{
    public static function make(): Builder
    {
        return Plan::query()
            ->with(self::eagerLoadRelations())
            ->where('status', true)
            ->whereHas('classes', fn($q) => self::filterAvailableClasses($q));
    }

    private static function eagerLoadRelations(): array
    {
        return array_merge(
            Plan::DEFAULT_RELATIONS,
            [
                'classes' => fn($q) => $q->withCount('registrations')
            ]
        );
    }

    private static function filterAvailableClasses(Builder $query): Builder
    {
        return $query->whereRaw('
            (SELECT COUNT(*)
             FROM class_registrations
             WHERE class_registrations.class_id = classes.id) < classes.capacity
        ');
    }
}
