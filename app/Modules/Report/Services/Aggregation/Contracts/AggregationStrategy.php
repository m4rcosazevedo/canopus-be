<?php

namespace App\Modules\Report\Services\Aggregation\Contracts;

interface AggregationStrategy
{
    public function initialize(): mixed;

    public function accumulate(mixed &$aggregation, float $value): void;

    public function finalize(mixed $aggregation): mixed;
}
