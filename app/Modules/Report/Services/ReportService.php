<?php

namespace App\Modules\Report\Services;

use App\Models\User;
use App\Modules\Report\Exports\GenericExport;
use App\Modules\Report\Jobs\GenerateReportJob;
use App\Modules\Report\Models\Report;
use Spatie\Browsershot\Browsershot;
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
        $date = Carbon::now()->format('YmdHis');
        $filename = 'reports/' . $date . '-' . $report->id . '-' . ($report->name ?? 'report') . '.' . $report->format;

        $tempDataFile = 'temp_data_' . $report->id . '.jsonl';

        try {
            $this->fetchAndStoreData($report, $tempDataFile);

            if ($report->format === 'pdf') {
                $this->generatePdf($report, $filename, $tempDataFile);
            } elseif (in_array($report->format, ['csv', 'xlsx'])) {
                $this->generateExcel($report, $filename, $tempDataFile);
            }

            $report->path = $filename;
            $report->save();

        } finally {
            if (Storage::disk('local')->exists($tempDataFile)) {
                Storage::disk('local')->delete($tempDataFile);
            }
        }
    }

    protected function fetchAndStoreData(Report $report, string $tempFile)
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

        $page = 1;

        Storage::disk('local')->put($tempFile, '');

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

            if (is_array($items) && count($items) > 0) {
                $processedChunk = $this->processData($items, $report->parameters['fields']);

                $content = '';
                foreach ($processedChunk as $row) {
                    $content .= json_encode($row) . "\n";
                }
                Storage::disk('local')->append($tempFile, $content);
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
    }

    protected function generatePdf(Report $report, string $filename, string $tempDataFile)
    {
        $layout = $this->calculateLayout($report->parameters['fields']);
        $chunks = $layout['chunks'];

        $queryDisplay = collect($report->parameters['queryDisplay'] ?? [])
            ->filter(fn($item) => isset($item['value']) && trim((string) $item['value']) !== '')
            ->values()
            ->toArray();

        $htmlFile = 'temp_html_' . $report->id . '.html';

        try {
            $html = view('reports.pdf_header', [
                'title' => $report->parameters['title'] ?? 'Relatório',
                'queryDisplay' => $queryDisplay,
            ])->render();
            Storage::disk('local')->put($htmlFile, $html);

            foreach ($chunks as $index => $chunkFields) {
                if ($index > 0) {
                    Storage::disk('local')->append($htmlFile, '<div class="page-break"></div>');
                }

                $rowsPerTable = 50;
                $rowCount = 0;

                $this->writeTableHeader($htmlFile, $chunkFields);

                $handle = fopen(Storage::disk('local')->path($tempDataFile), 'r');
                if ($handle) {
                    while (($line = fgets($handle)) !== false) {
                        $row = json_decode($line, true);
                        if ($row) {
                            if ($rowCount > 0 && $rowCount % $rowsPerTable === 0) {
                                Storage::disk('local')->append($htmlFile, '</tbody></table>');
                                Storage::disk('local')->append($htmlFile, '<div class="page-break"></div>');
                                $this->writeTableHeader($htmlFile, $chunkFields);
                            }

                            $tr = '<tr>';
                            foreach ($chunkFields as $field) {
                                $value = $row[$field['title']] ?? '';
                                $tr .= '<td>' . htmlspecialchars((string)$value) . '</td>';
                            }
                            $tr .= '</tr>';
                            Storage::disk('local')->append($htmlFile, $tr);
                            $rowCount++;
                        }
                    }
                    fclose($handle);
                }

                Storage::disk('local')->append($htmlFile, '</tbody></table>');
            }

            Storage::disk('local')->append($htmlFile, '</body></html>');

            $fullHtml = Storage::disk('local')->get($htmlFile);

            // Limpar HTML temporário
            Storage::disk('local')->delete($htmlFile);

            // ----------------------------------------------------------------
            // INÍCIO DA NOVA LÓGICA DO BROWSERSHOT
            // ----------------------------------------------------------------
            $absoluteSavePath = Storage::disk('local')->path($filename);

            $browsershot = Browsershot::html($fullHtml)
                ->format('A4')
                ->margins(10, 10, 10, 10)
                ->showBackground() // Útil se o seu CSS tiver cores de fundo nas tabelas
                ->setChromePath('/usr/bin/chromium')
                ->addChromiumArguments([
                    'no-sandbox',
                    'disable-setuid-sandbox',
                    'disable-dev-shm-usage', // Crítico para Docker
                    'disable-extensions',
                    'disable-gpu',
                    'no-zygote',
                    'single-process',
                ]);

            // Se o cálculo do layout definiu como landscape, aplicamos no Browsershot
            if ($layout['orientation'] === 'landscape') {
                $browsershot->landscape();
            }

            // Opcional: Se o node/npm não estiverem no PATH padrão do usuário do Docker,
            // descomente e ajuste as linhas abaixo:
            // ->setNodeBinary('/usr/bin/node')
            // ->setNpmBinary('/usr/bin/npm')

            $browsershot->save($absoluteSavePath);
            // ----------------------------------------------------------------
            // FIM DA LÓGICA DO BROWSERSHOT
            // ----------------------------------------------------------------

        } catch (\Throwable $e) {
            if (Storage::disk('local')->exists($htmlFile)) {
                Storage::disk('local')->delete($htmlFile);
            }
            throw $e;
        }
    }

    protected function writeTableHeader(string $htmlFile, array $fields)
    {
        $tableHeader = '<table><thead><tr>';
        foreach ($fields as $field) {
            $tableHeader .= '<th>' . $field['title'] . '</th>';
        }
        $tableHeader .= '</tr></thead><tbody>';
        Storage::disk('local')->append($htmlFile, $tableHeader);
    }

    protected function generateExcel(Report $report, string $filename, string $tempDataFile)
    {
        $generator = function() use ($tempDataFile) {
            $handle = fopen(Storage::disk('local')->path($tempDataFile), 'r');
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $row = json_decode($line, true);
                    if ($row) {
                        yield $row;
                    }
                }
                fclose($handle);
            }
        };

        Excel::store(new GenericExport($generator(), $report->parameters['fields']), $filename, 'local');
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
