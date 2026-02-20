<?php

namespace App\Modules\Report\Contracts;

interface ReportStorageInterface
{
    public function putTemp(string $filename, string $content): void;
    public function appendTemp(string $filename, string $content): void;
    public function getTempContent(string $filename): string;
    public function getTempPath(string $filename): string;
    public function getTempRelativePath(string $filename): string;
    public function existsTemp(string $filename): bool;

    /**
     * Retorna um resource de stream (fopen) para leitura de grandes arquivos.
     * @return resource|false
     */
    public function getTempStream(string $filename);
    public function deleteTemp(string|array $filenames): void;

    /**
     * Move o arquivo gerado do workspace temporário para o destino final (S3, Azure, Local, etc).
     */
    public function moveToFinalDestination(string $localTempFilename, string $destinationPath): void;
}
