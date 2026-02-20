<?php

namespace App\Modules\Report\Contracts;

use App\Modules\Report\Models\Report;

interface ReportGeneratorInterface
{
    /**
     * Gera o arquivo final do relatório com base nos dados temporários.
     */
    public function generate(Report $report, string $tempDataFile): string;
}
