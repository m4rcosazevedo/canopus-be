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

        $headers = [
            'Accept' => 'application/json',
        ];

        if ($report->authenticated) {
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

        $response = Http::withHeaders($headers)->get($report->endpoint, $queryParams);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch data from endpoint: ' . $response->status() . ' - ' . $response->body());
        }

        $json = $response->json();

        if (isset($json['data']) && is_array($json['data'])) {
            return $json['data'];
        }

        return $json;
    }

    protected function processData($data, $fields)
    {
        $processed = [];

        foreach ($data as $item) {
            // Identify all array paths that need expansion (contain .*. )
            $expansionPaths = [];
            foreach ($fields as $field) {
                // name is the path now
                if (strpos($field['name'], '.*.') !== false) {
                    $parts = explode('.*.', $field['name']);
                    $basePath = $parts[0];
                    if (!in_array($basePath, $expansionPaths)) {
                        $expansionPaths[] = $basePath;
                    }
                }
            }

            // If no expansion needed, just process single row
            if (empty($expansionPaths)) {
                $processed[] = $this->extractRow($item, $fields);
                continue;
            }

            // Let's build a list of arrays to iterate over.
            $arraysToExpand = [];
            foreach ($expansionPaths as $path) {
                $arrayData = data_get($item, $path);
                if (is_array($arrayData) && count($arrayData) > 0) {
                    $arraysToExpand[$path] = $arrayData;
                } else {
                    $arraysToExpand[$path] = [null];
                }
            }

            // Recursive function to generate combinations.
            $combinations = $this->generateCombinations($arraysToExpand);

            foreach ($combinations as $combination) {
                $row = [];
                foreach ($fields as $field) {
                    $path = $field['name']; // name is the path
                    $value = null;

                    // Check if this field belongs to one of the expanded arrays
                    $matchedExpansion = false;
                    foreach ($expansionPaths as $expansionPath) {
                        if (strpos($path, $expansionPath . '.*.') === 0) {
                            // It belongs to this expansion
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
                        // Regular field or specific index field (documents.0.name)
                        $value = data_get($item, $path);
                    }

                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }
                    // title is the header/key
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
            $value = data_get($item, $field['name']); // name is the path
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $row[$field['title']] = $value; // title is the header/key
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
