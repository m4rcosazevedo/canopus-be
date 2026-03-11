<?php

namespace App\Modules\Report\Services\Aggregation\Strategies;

use App\Modules\Report\Services\Aggregation\Contracts\AggregationStrategy;

class SubAggregation implements AggregationStrategy
{
    public function initialize(): mixed
    {
        return [
            'total' => 0,
            'is_first' => true
        ];
    }

    public function accumulate(mixed &$aggregation, float $value): void
    {
        if ($aggregation['is_first']) {
            $aggregation['total'] = $value;
            $aggregation['is_first'] = false;
        } else {
            $aggregation['total'] -= $value;
        }
    }

    public function finalize(mixed $aggregation): mixed
    {
        return $aggregation['total'];
    }
}
