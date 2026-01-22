<?php

namespace App\Services;

use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenericExport;

class ReportService
{
    public function generate(Report $report)
    {
        $data = $this->fetchDataFromEndpoint($report);
        $processedData = $this->processData($data, $report->parameters['fields']);

        $date = Carbon::now()->format('YmdHis');

        $filename = 'reports/' . $date . '-' . $report->id . '-' . ($report->name ?? 'report') . '.' . $report->format;

        if ($report->format === 'pdf') {
            $pdf = Pdf::loadView($report->template ?? 'reports.default', [
                'data' => $processedData,
                'title' => $report->parameters['title'] ?? 'Relatório',
                'queryDisplay' => $report->parameters['queryDisplay'] ?? [],
                'fields' => $report->parameters['fields']
            ]);
            Storage::disk('local')->put($filename, $pdf->output());
        } elseif (in_array($report->format, ['csv', 'xlsx'])) {
             Excel::store(new GenericExport($processedData, $report->parameters['fields']), $filename, 'local');
        }

        $report->path = $filename;
        $report->save();
    }

    protected function fetchDataFromEndpoint(Report $report)
    {
        $queryParams = $report->parameters['queryParams'] ?? [];
        $headers = ['Accept' => 'application/json'];

        if ($report->authenticated || $report->token) {
            $token = $report->token;

            if (empty($token)) {
                $user = $report->user;
                if ($user) {
                    $token = $user->createToken('report-generation')->plainTextToken;
                }
            }

            if ($token) {
                $headers['Authorization'] = 'Bearer ' . $token;
            }
        }

        $paginateConfig = $report->parameters['paginate'] ?? null;
        $contentKey = $report->parameters['contentKey'] ?? null;
        $format = $report->parameters['format'] ?? 'array';

        $allItems = [];
        $page = 1;

        do {
            if ($paginateConfig) {
                $queryParams[$paginateConfig['queryFieldKey']] = $page;
            }

            $response = Http::withHeaders($headers)->get($report->endpoint, $queryParams);

            if ($response->failed()) {
                throw new \Exception('Failed to fetch data from endpoint: ' . $response->status() . ' - ' . $response->body());
            }

            $json = $response->json();

            $items = $contentKey ? data_get($json, $contentKey) : $json;

            if ($format === 'object') {
                $items = [$items];
            }

            if (is_array($items)) {
                $allItems = array_merge($allItems, $items);
            }

            $shouldContinue = false;
            if ($paginateConfig) {
                $currentPage = data_get($json, $paginateConfig['currentPage']);
                $lastPage = data_get($json, $paginateConfig['lastPage']);

                if ($currentPage && $lastPage && $currentPage < $lastPage) {
                    $page++;
                    $shouldContinue = true;
                }
            }

        } while ($shouldContinue);

        return $allItems;
    }

    protected function processData($data, $fields)
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

    protected function extractRow($item, $fields)
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

    protected function generateCombinations($arrays)
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
