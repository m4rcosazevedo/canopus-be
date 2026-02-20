<?php

namespace App\Modules\Report\DataSources;

use App\Modules\Report\Contracts\DataSourceInterface;
use App\Modules\Report\Models\Report;
use App\Modules\Report\Processors\DataProcessor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Exception;

class ApiDataSource implements DataSourceInterface
{
    public function __construct(
        protected DataProcessor $processor
    ) {}

    public function fetchAndStore(Report $report, string $tempFile): void
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
                throw new Exception('Failed to fetch data from endpoint: ' . $response->status() . ' - ' . $response->body());
            }

            $json = $response->json();

            $items = $contentKey ? data_get($json, $contentKey) : $json;

            if ($format === 'object') {
                $items = [$items];
            }

            if (is_array($items) && count($items) > 0) {
                // Utiliza o processor injetado para formatar os dados
                $processedChunk = $this->processor->process($items, $report->parameters['fields']);

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
}
