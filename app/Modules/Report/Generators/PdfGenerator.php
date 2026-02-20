<?php

namespace App\Modules\Report\Generators;

use App\Modules\Report\Contracts\ReportGeneratorInterface;
use App\Modules\Report\Models\Report;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Throwable;

class PdfGenerator implements ReportGeneratorInterface
{
    public function generate(Report $report, string $filename, string $tempDataFile): void
    {
        $fields = $report->parameters['fields'];
        $layout = $this->calculateLayout($fields);

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
            $this->writeTableHeader($htmlFile, $fields);

            $handle = fopen(Storage::disk('local')->path($tempDataFile), 'r');
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $row = json_decode($line, true);
                    if ($row) {
                        $tr = '<tr>';
                        foreach ($fields as $field) {
                            $value = $row[$field['title']] ?? '';
                            $tr .= '<td>' . htmlspecialchars((string)$value) . '</td>';
                        }
                        $tr .= '</tr>';
                        Storage::disk('local')->append($htmlFile, $tr);
                    }
                }
                fclose($handle);
            }

            Storage::disk('local')->append($htmlFile, '</tbody></table></body></html>');

            $fullHtml = Storage::disk('local')->get($htmlFile);
            Storage::disk('local')->delete($htmlFile);

            $absoluteSavePath = Storage::disk('local')->path($filename);

            $browsershot = Browsershot::html($fullHtml)
                ->format('A4')
                ->margins(10, 10, 10, 10)
                ->showBackground()
                ->setChromePath('/usr/bin/chromium')
                ->addChromiumArguments([
                    'no-sandbox',
                    'disable-setuid-sandbox',
                    'disable-dev-shm-usage',
                    'disable-extensions',
                    'disable-gpu',
                    'no-zygote',
                    'single-process',
                ]);

            if ($layout['orientation'] === 'landscape') {
                $browsershot->landscape();
            }

            $browsershot->save($absoluteSavePath);

        } catch (Throwable $e) {
            if (Storage::disk('local')->exists($htmlFile)) {
                Storage::disk('local')->delete($htmlFile);
            }
            throw $e;
        }
    }

    protected function writeTableHeader(string $htmlFile, array $fields): void
    {
        $tableHeader = '<table class="table-report"><thead><tr>';
        foreach ($fields as $field) {
            $tableHeader .= '<th>' . htmlspecialchars($field['title']) . '</th>';
        }
        $tableHeader .= '</tr></thead><tbody>';
        Storage::disk('local')->append($htmlFile, $tableHeader);
    }

    protected function calculateLayout(array $fields): array
    {
        $maxPortrait = 7;
        return [
            'orientation' => count($fields) <= $maxPortrait ? 'portrait' : 'landscape',
        ];
    }
}
