<?php

namespace App\Modules\Report\Services;

use App\Models\User;
use App\Modules\Report\Contracts\DataSourceInterface;
use App\Modules\Report\Contracts\ReportGeneratorInterface;
use App\Modules\Report\Contracts\ReportStorageInterface;
use App\Modules\Report\Generators\ExcelGenerator;
use App\Modules\Report\Generators\PdfGenerator;
use App\Modules\Report\Jobs\GenerateReportJob;
use App\Modules\Report\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Throwable;

class ReportService
{
    public function __construct(
        protected DataSourceInterface $dataSource,
        protected ReportStorageInterface $storage
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
        $finalDestinationFilename = 'reports/' . $date . '-' . $report->id . '-' . ($report->name ?? 'report') . '.' . $report->format;
        $tempDataFile = 'temp_data_' . $report->id . '.jsonl';
        $localGeneratedFile = null;

        try {
            // 1. Busca os dados e salva num arquivo temporário .jsonl
            $this->dataSource->fetchAndStore($report, $tempDataFile);

            // 2. Descobre quem vai gerar o arquivo (PDF ou Excel)
            $generator = $this->resolveGenerator($report->format);

            // 3. O generator processa o .jsonl e cria o arquivo final localmente (ex: output.pdf)
            $localGeneratedFile = $generator->generate($report, $tempDataFile);

            // 4. O Storage Manager pega o arquivo gerado localmente e envia para a Nuvem/Destino Final
            $this->storage->moveToFinalDestination($localGeneratedFile, $finalDestinationFilename);

            // 5. Atualiza o banco com o caminho lá na nuvem (ou destino final escolhido)
            $report->path = $finalDestinationFilename;
            $report->save();

        } finally {
            $filesToDelete = array_filter([$tempDataFile, $localGeneratedFile]);
            $this->storage->deleteTemp($filesToDelete);
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
