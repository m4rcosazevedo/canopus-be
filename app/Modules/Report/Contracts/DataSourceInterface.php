<?php

namespace App\Modules\Report\Contracts;

use App\Modules\Report\Models\Report;

interface DataSourceInterface
{
    /**
     * Busca os dados da origem e os armazena temporariamente.
     */
    public function fetchAndStore(Report $report, string $tempFile): void;
}
