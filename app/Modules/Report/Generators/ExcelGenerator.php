<?php

namespace App\Modules\Report\Generators;

use App\Modules\Report\Contracts\ReportGeneratorInterface;
use App\Modules\Report\Exports\GenericExport;
use App\Modules\Report\Models\Report;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExcelGenerator implements ReportGeneratorInterface
{
    public function generate(Report $report, string $filename, string $tempDataFile): void
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
}
