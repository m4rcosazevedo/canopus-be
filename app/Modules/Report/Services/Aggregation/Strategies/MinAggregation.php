<?php

namespace App\Modules\Report\Services\Aggregation\Strategies;

use App\Modules\Report\Services\Aggregation\Contracts\AggregationStrategy;

class MinAggregation implements AggregationStrategy
{
    public function initialize(): mixed
    {
        return null;
    }

    public function accumulate(mixed &$aggregation, float $value): void
    {
        if ($aggregation === null || $value < $aggregation) {
            $aggregation = $value;
        }
    }

    public function finalize(mixed $aggregation): mixed
    {
        return $aggregation;
    }
}
