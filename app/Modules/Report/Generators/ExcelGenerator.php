<?php

namespace App\Modules\Report\Generators;

use App\Modules\Report\Contracts\ReportGeneratorInterface;
use App\Modules\Report\Contracts\ReportStorageInterface;
use App\Modules\Report\Exports\GenericExport;
use App\Modules\Report\Models\Report;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class ExcelGenerator implements ReportGeneratorInterface
{
    public function __construct(
        protected ReportStorageInterface $storage
    ) {}

    public function generate(Report $report, string $tempDataFile): string
    {
        $extension = $report->format === 'csv' ? 'csv' : 'xlsx';
        $outputFile = 'generated_excel_' . $report->id . '_' . Str::random(8) . '.' . $extension;

        $generator = function() use ($tempDataFile) {
            $handle = $this->storage->getTempStream($tempDataFile);

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

        $export = new GenericExport($generator(), $report->parameters['fields']);

        $relativePath = $this->storage->getTempRelativePath($outputFile);

        Excel::store($export, $relativePath, 'local');

        return $outputFile;
    }
}
