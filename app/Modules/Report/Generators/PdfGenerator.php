<?php

namespace App\Modules\Report\Generators;

use App\Modules\Report\Contracts\ReportGeneratorInterface;
use App\Modules\Report\Contracts\ReportStorageInterface;
use App\Modules\Report\Models\Report;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Throwable;

class PdfGenerator implements ReportGeneratorInterface
{
    public function __construct(
        protected ReportStorageInterface $storage
    ) {}

    public function generate(Report $report, string $tempDataFile): string
    {
        $fields = $report->parameters['fields'];
        $layout = $this->calculateLayout($fields);

        $queryDisplay = collect($report->parameters['queryDisplay'] ?? [])
            ->filter(fn($item) => isset($item['value']) && trim((string) $item['value']) !== '')
            ->values()
            ->toArray();

        $htmlFile = 'temp_html_' . $report->id . '.html';
        $outputPdfFile = 'generated_pdf_' . $report->id . '.pdf';

        try {
            $html = view('reports.pdf_header', [
                'title' => $report->parameters['title'] ?? 'Relatório',
                'queryDisplay' => $queryDisplay,
            ])->render();

            $this->storage->putTemp($htmlFile, $html);
            $this->writeTableHeader($htmlFile, $fields);

            $count = 0;
            $handle = $this->storage->getTempStream($tempDataFile);
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $row = json_decode($line, true);
                    if ($row) {
                        $count++;
                        $tr = '<tr>';
                        foreach ($fields as $field) {
                            $value = $row[$field['title']] ?? '';
                            $tr .= '<td>' . htmlspecialchars((string)$value) . '</td>';
                        }
                        $tr .= '</tr>';
                        $this->storage->appendTemp($htmlFile, $tr);
                    }
                }
                fclose($handle);
            }

            if (isset($report->parameters['footerPDF']) && $report->parameters['footerPDF'] === 'count') {
                $colspan = count($fields);
                $footerHtml = "<tr><td colspan='{$colspan}' style='text-align: right; font-weight: bold; background-color: #f2f2f2;'>Total de Registros: {$count}</td></tr>";
                $this->storage->appendTemp($htmlFile, $footerHtml);
            }

            $this->storage->appendTemp($htmlFile, '</tbody></table></body></html>');

            $fullHtml = $this->storage->getTempContent($htmlFile);
            $this->storage->deleteTemp($htmlFile);

            $absoluteSavePath = $this->storage->getTempPath($outputPdfFile);

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

            return $outputPdfFile;

        } catch (Throwable $e) {
            if ($this->storage->existsTemp($htmlFile)) {
                $this->storage->deleteTemp([$htmlFile, $outputPdfFile]);
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

        $this->storage->appendTemp($htmlFile, $tableHeader);
    }

    protected function calculateLayout(array $fields): array
    {
        $maxPortrait = 7;
        return [
            'orientation' => count($fields) <= $maxPortrait ? 'portrait' : 'landscape',
        ];
    }
}
