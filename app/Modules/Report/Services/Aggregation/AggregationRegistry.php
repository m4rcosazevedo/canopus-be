<?php

namespace App\Modules\Report\Services\Aggregation;

use App\Modules\Report\Services\Aggregation\Strategies\AverageAggregation;
use App\Modules\Report\Services\Aggregation\Strategies\CountAggregation;
use App\Modules\Report\Services\Aggregation\Strategies\MaxAggregation;
use App\Modules\Report\Services\Aggregation\Strategies\MinAggregation;
use App\Modules\Report\Services\Aggregation\Strategies\SubAggregation;
use App\Modules\Report\Services\Aggregation\Strategies\SumAggregation;

class AggregationRegistry
{
    public static function get(): array
    {
        return [
            'sum'   => new SumAggregation(),
            'avg'   => new AverageAggregation(),
            'sub'   => new SubAggregation(),
            'count' => new CountAggregation(),
            'min'   => new MinAggregation(),
            'max'   => new MaxAggregation(),
        ];
    }
}
