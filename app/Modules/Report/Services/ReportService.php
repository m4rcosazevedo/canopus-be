<?php

namespace App\Modules\Report\Services;

use App\Models\User;
use App\Modules\Report\Exports\GenericExport;
use App\Modules\Report\Jobs\GenerateReportJob;
use App\Modules\Report\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReportService
{
    public function createReport(User $user, array $data): Report
    {
        $report = Report::create([
            'user_id' => $user->id,
            'name' => $data['name'] ?? 'report_' . now()->timestamp,
            'format' => $data['options']['type'],
            'template' => $data['template'] ?? null,
            'endpoint' => $data['endpoint'],
            'authenticated' => $data['authenticated'] ?? false,
            'token' => $data['token'] ?? null,
            'parameters' => $data['options'],
            'status' => 'pending',
        ]);

        GenerateReportJob::dispatch($report);

        return $report;
    }

    public function generate(Report $report)
    {
        $data = $this->fetchDataFromEndpoint($report);
        $processedData = $this->processData($data, $report->parameters['fields']);

        $date = Carbon::now()->format('YmdHis');

        $filename = 'reports/' . $date . '-' . $report->id . '-' . ($report->name ?? 'report') . '.' . $report->format;

        if ($report->format === 'pdf') {
            $layout = $this->calculateLayout($report->parameters['fields']);

            $queryDisplay = collect($report->parameters['queryDisplay'] ?? [])
                ->filter(function ($item) {
                    return isset($item['value']) && trim((string) $item['value']) !== '';
                })
                ->values()
                ->toArray();


            $pdf = Pdf::loadView($report->template ?? 'reports.default', [
                'data' => $processedData,
                'title' => $report->parameters['title'] ?? 'Relatório',
                'queryDisplay' => $queryDisplay,
                'fields' => $report->parameters['fields'],
                'fieldChunks' => $layout['chunks']
            ]);

            $pdf->setPaper('a4', $layout['orientation']);

            Storage::disk('local')->put($filename, $pdf->output());
        } elseif (in_array($report->format, ['csv', 'xlsx'])) {
             Excel::store(new GenericExport($processedData, $report->parameters['fields']), $filename, 'local');
        }

        $report->path = $filename;
        $report->save();
    }

    protected function calculateLayout($fields)
    {
        $maxPortrait = 7;
        $maxLandscape = 12;

        $count = count($fields);

        if ($count <= $maxPortrait) {
            return [
                'orientation' => 'portrait',
                'chunks' => [$fields]
            ];
        }

        if ($count <= $maxLandscape) {
            return [
                'orientation' => 'landscape',
                'chunks' => [$fields]
            ];
        }

        return [
            'orientation' => 'landscape',
            'chunks' => array_chunk($fields, $maxLandscape)
        ];
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

        $endpoint = $report->endpoint;
        if (str_starts_with($endpoint, '/')) {
            $endpoint = rtrim(config('app.url'), '/') . $endpoint;
        }

        $allItems = [];
        $page = 1;

        do {
            if ($paginateConfig) {
                $queryParams[$paginateConfig['queryFieldKey']] = $page;
            }

            $response = Http::withHeaders($headers)->get($endpoint, $queryParams);

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
