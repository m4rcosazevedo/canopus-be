<?php

namespace App\Modules\Report\Services;

use App\Models\User;
use App\Modules\Report\Contracts\DataSourceInterface;
use App\Modules\Report\Contracts\ReportGeneratorInterface;
use App\Modules\Report\Generators\ExcelGenerator;
use App\Modules\Report\Generators\PdfGenerator;
use App\Modules\Report\Jobs\GenerateReportJob;
use App\Modules\Report\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class ReportService
{
    public function __construct(
        protected DataSourceInterface $dataSource
    ) {}

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

    public function generate(Report $report): void
    {
        $date = Carbon::now()->format('YmdHis');
        $filename = 'reports/' . $date . '-' . $report->id . '-' . ($report->name ?? 'report') . '.' . $report->format;
        $tempDataFile = 'temp_data_' . $report->id . '.jsonl';

        try {
            // 1. Busca e armazena os dados
            $this->dataSource->fetchAndStore($report, $tempDataFile);

            // 2. Determina a estratégia de geração (Factory Method)
            $generator = $this->resolveGenerator($report->format);

            // 3. Gera o relatório
            $generator->generate($report, $filename, $tempDataFile);

            $report->path = $filename;
            $report->save();

        } finally {
            if (Storage::disk('local')->exists($tempDataFile)) {
                Storage::disk('local')->delete($tempDataFile);
            }
        }
    }

    /**
     * Factory method para instanciar o gerador correto baseado no formato.
     */
    protected function resolveGenerator(string $format): ReportGeneratorInterface
    {
        return match ($format) {
            'pdf' => app(PdfGenerator::class),
            'csv', 'xlsx' => app(ExcelGenerator::class),
            default => throw new InvalidArgumentException("Formato de relatório não suportado: {$format}"),
        };
    }
}
