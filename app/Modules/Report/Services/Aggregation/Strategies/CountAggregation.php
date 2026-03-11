<?php

namespace App\Modules\Report\Services\Aggregation\Strategies;

use App\Modules\Report\Services\Aggregation\Contracts\AggregationStrategy;

class CountAggregation implements AggregationStrategy
{
    public function initialize(): mixed
    {
        return 0;
    }

    public function accumulate(mixed &$aggregation, float $value): void
    {
        $aggregation++;
    }

    public function finalize(mixed $aggregation): mixed
    {
        return $aggregation;
    }
}
