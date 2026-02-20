<?php

namespace App\Modules\Report\Processors;

class DataProcessor
{
    public function process(array $data, array $fields): array
    {
        $processed = [];

        foreach ($data as $item) {
            $expansionPaths = [];
            foreach ($fields as $field) {
                if (str_contains($field['name'], '.*.')) {
                    $parts = explode('.*.', $field['name']);
                    $basePath = $parts[0];
                    if (!in_array($basePath, $expansionPaths)) {
                        $expansionPaths[] = $basePath;
                    }
                }
            }

            if (empty($expansionPaths)) {
                $processed[] = $this->extractRow($item, $fields);
                continue;
            }

            $arraysToExpand = [];
            foreach ($expansionPaths as $path) {
                $arrayData = data_get($item, $path);
                if (is_array($arrayData) && count($arrayData) > 0) {
                    $arraysToExpand[$path] = $arrayData;
                } else {
                    $arraysToExpand[$path] = [null];
                }
            }

            $combinations = $this->generateCombinations($arraysToExpand);

            foreach ($combinations as $combination) {
                $row = [];
                foreach ($fields as $field) {
                    $path = $field['name'];
                    $value = null;

                    $matchedExpansion = false;
                    foreach ($expansionPaths as $expansionPath) {
                        if (str_starts_with($path, $expansionPath . '.*.')) {
                            $subItem = $combination[$expansionPath];
                            if ($subItem) {
                                $subPath = substr($path, strlen($expansionPath . '.*.'));
                                $value = data_get($subItem, $subPath);
                            }
                            $matchedExpansion = true;
                            break;
                        }
                    }

                    if (!$matchedExpansion) {
                        $value = data_get($item, $path);
                    }

                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }
                    $row[$field['title']] = $value;
                }
                $processed[] = $row;
            }
        }

        return $processed;
    }

    protected function extractRow(array|object $item, array $fields): array
    {
        $row = [];
        foreach ($fields as $field) {
            $value = data_get($item, $field['name']);
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $row[$field['title']] = $value;
        }
        return $row;
    }

    protected function generateCombinations(array $arrays): array
    {
        if (empty($arrays)) {
            return [[]];
        }

        $keys = array_keys($arrays);
        $key = $keys[0];
        $values = $arrays[$key];
        unset($arrays[$key]);

        $restCombinations = $this->generateCombinations($arrays);
        $result = [];

        foreach ($values as $value) {
            foreach ($restCombinations as $combination) {
                $result[] = array_merge([$key => $value], $combination);
            }
        }

        return $result;
    }
}
