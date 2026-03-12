<?php

namespace App\Modules\Report\Generators;

use App\Modules\Report\Contracts\ReportGeneratorInterface;
use App\Modules\Report\Contracts\ReportStorageInterface;
use App\Modules\Report\Models\Report;
use App\Modules\Report\Renderers\RowRenderer;
use App\Modules\Report\Services\Aggregation\AggregationEngine;
use App\Modules\Report\Services\FormatterService;
use App\Modules\Report\Support\BufferedWriter;
use Spatie\Browsershot\Browsershot;
use Throwable;

class PdfGenerator implements ReportGeneratorInterface
{
    private const MAX_PORTRAIT_FIELDS = 7;
    private const BUFFER_LIMIT = 100;
    private const CHROMIUM_PATH = '/usr/bin/chromium';
    private const CHROMIUM_ARGUMENTS = [
        'no-sandbox',
        'disable-setuid-sandbox',
        'disable-dev-shm-usage',
        'disable-extensions',
        'disable-gpu',
        'no-zygote',
        'single-process',
    ];

    public function __construct(
        protected ReportStorageInterface $storage
    ) {}

    public function generate(Report $report, string $tempDataFile): string
    {
        $fields     = $report->parameters['fields'];
        $htmlFile   = "temp_html_{$report->id}.html";
        $outputFile = "generated_pdf_{$report->id}.pdf";

        try {
            $this->buildHtml($report, $fields, $tempDataFile, $htmlFile);
            $this->renderPdf($htmlFile, $outputFile, $fields);

            return $outputFile;
        } catch (Throwable $e) {
            $this->cleanupTempFiles($htmlFile, $outputFile);
            throw $e;
        }
    }

    private function buildHtml(Report $report, array $fields, string $tempDataFile, string $htmlFile): void
    {
        $engine   = new AggregationEngine();
        $formatter = new FormatterService();
        $renderer = new RowRenderer($formatter);
        $writer   = new BufferedWriter(storage: $this->storage, file: $htmlFile, limit: self::BUFFER_LIMIT);

        $engine->initialize($fields);

        $header = view('reports.pdf_header', [
            'title'        => $report->parameters['title'] ?? 'Relatório',
            'queryDisplay' => $this->filterQueryDisplay($report->parameters['queryDisplay'] ?? []),
        ])->render();

        $this->storage->putTemp($htmlFile, $header);
        $this->storage->appendTemp($htmlFile, $this->renderTableHeader($fields));

        $count = $this->streamRows($tempDataFile, $engine, $renderer, $writer, $fields);

        $writer->append($renderer->renderAggregation($fields, $engine->result()));

        if ($this->shouldDisplayTotalRecords($report)) {
            $writer->append($renderer->renderFooter(count($fields), $count));
        }

        $writer->flush();

        $this->storage->appendTemp($htmlFile, '</tbody></table></body></html>');
    }

    private function streamRows(
        string $tempDataFile,
        AggregationEngine $engine,
        RowRenderer $renderer,
        BufferedWriter $writer,
        array $fields
    ): int {
        $count  = 0;
        $handle = $this->storage->getTempStream($tempDataFile);

        if (!$handle) {
            return $count;
        }

        while (($line = fgets($handle)) !== false) {
            $row = json_decode($line, true);

            if (!$row) {
                continue;
            }

            $count++;
            $engine->accumulate($row);
            $writer->append($renderer->render($fields, $row));
        }

        fclose($handle);

        return $count;
    }

    private function renderPdf(string $htmlFile, string $outputFile, array $fields): void
    {
        $fullHtml    = $this->storage->getTempContent($htmlFile);
        $orientation = $this->resolveOrientation($fields);
        $outputPath  = $this->storage->getTempPath($outputFile);

        $this->storage->deleteTemp($htmlFile);

        $browsershot = Browsershot::html($fullHtml)
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->showBackground()
            ->setChromePath(self::CHROMIUM_PATH)
            ->addChromiumArguments(self::CHROMIUM_ARGUMENTS);

        if ($orientation === 'landscape') {
            $browsershot->landscape();
        }

        $browsershot->save($outputPath);
    }

    private function renderTableHeader(array $fields): string
    {
        $headers = collect($fields)
            ->map(fn($field) => '<th>' . htmlspecialchars($field['title']) . '</th>')
            ->implode('');

        return "<table><thead><tr>{$headers}</tr></thead><tbody>";
    }

    private function filterQueryDisplay(array $queryDisplay): array
    {
        return collect($queryDisplay)
            ->filter(fn($item) => isset($item['value']) && trim((string) $item['value']) !== '')
            ->values()
            ->toArray();
    }

    private function resolveOrientation(array $fields): string
    {
        return count($fields) <= self::MAX_PORTRAIT_FIELDS ? 'portrait' : 'landscape';
    }

    private function shouldDisplayTotalRecords(Report $report): bool
    {
        return (bool) ($report->parameters['footer']['displayTotalRecords'] ?? false);
    }

    private function cleanupTempFiles(string ...$files): void
    {
        $existing = array_filter($files, fn($f) => $this->storage->existsTemp($f));

        if ($existing) {
            $this->storage->deleteTemp($existing);
        }
    }
}
