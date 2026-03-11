<?php

namespace App\Modules\Report\Services\Aggregation\Strategies;

use App\Modules\Report\Services\Aggregation\Contracts\AggregationStrategy;

class SumAggregation implements AggregationStrategy
{
    public function initialize(): mixed
    {
        return 0;
    }

    public function accumulate(mixed &$aggregation, float $value): void
    {
        $aggregation += $value;
    }

    public function finalize(mixed $aggregation): mixed
    {
        return $aggregation;
    }
}
