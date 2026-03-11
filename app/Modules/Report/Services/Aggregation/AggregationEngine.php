<?php

namespace App\Modules\Report\Services\Aggregation;

class AggregationEngine
{
    protected array $strategies;

    protected array $aggregations = [];

    protected array $fieldsWithOps = [];

    protected bool $finalized = false;

    public function __construct()
    {
        $this->strategies = AggregationRegistry::get();
    }

    public function initialize(array $fields): void
    {
        foreach ($fields as $field) {

            $operation = $field['operation'] ?? null;

            if (!isset($this->strategies[$operation])) {
                continue;
            }

            $fieldName = $field['title'];

            $strategy = $this->strategies[$operation];

            $this->fieldsWithOps[$fieldName] = $strategy;
            $this->aggregations[$fieldName] = $strategy->initialize();
        }
    }

    public function accumulate(array $row): void
    {
        foreach ($this->fieldsWithOps as $field => $strategy) {

            $value = (float) data_get($row, $field, 0);

            $strategy->accumulate(
                $this->aggregations[$field],
                $value
            );
        }
    }

    public function result(): array
    {
        if ($this->finalized) {
            return $this->aggregations;
        }

        foreach ($this->fieldsWithOps as $field => $strategy) {
            $this->aggregations[$field] =
                $strategy->finalize($this->aggregations[$field]);
        }

        $this->finalized = true;

        return $this->aggregations;
    }
}
