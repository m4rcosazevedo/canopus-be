<?php

namespace App\Modules\Report\Services\Aggregation\Strategies;

use App\Modules\Report\Services\Aggregation\Contracts\AggregationStrategy;

class AverageAggregation implements AggregationStrategy
{
    public function initialize(): mixed
    {
        return ['sum' => 0, 'count' => 0];
    }

    public function accumulate(mixed &$aggregation, float $value): void
    {
        $aggregation['sum'] += $value;
        $aggregation['count']++;
    }

    public function finalize(mixed $aggregation): mixed
    {
        return $aggregation['count']
            ? $aggregation['sum'] / $aggregation['count']
            : 0;
    }
}
