<?php

namespace App\Modules\Report\Services;

use App\Modules\Report\Contracts\ReportStorageInterface;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ReportStorageManager implements ReportStorageInterface
{
    protected string $finalDisk;
    protected string $tempDir = 'temp_reports';

    public function __construct()
    {
        // Aqui você puxa do .env (ex: REPORT_DISK=s3). Se não existir, usa 'local'.
        $this->finalDisk = config('reports.disk', 'local');

        if (!Storage::disk('local')->exists($this->tempDir)) {
            Storage::disk('local')->makeDirectory($this->tempDir);
        }
    }

    public function putTemp(string $filename, string $content): void
    {
        Storage::disk('local')->put($this->tempPath($filename), $content);
    }

    public function appendTemp(string $filename, string $content): void
    {
        Storage::disk('local')->append($this->tempPath($filename), $content);
    }

    public function getTempContent(string $filename): string
    {
        return Storage::disk('local')->get($this->tempPath($filename));
    }

    public function getTempPath(string $filename): string
    {
        return Storage::disk('local')->path($this->tempPath($filename));
    }

    public function getTempRelativePath(string $filename): string
    {
        return $this->tempPath($filename);
    }

    public function getTempStream(string $filename)
    {
        return fopen($this->getTempPath($filename), 'r');
    }

    public function deleteTemp(string|array $filenames): void
    {
        $filenames = (array) $filenames;
        foreach ($filenames as $file) {
            $path = $this->tempPath($file);
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
        }
    }

    public function moveToFinalDestination(string $localTempFilename, string $destinationPath): void
    {
        $localPath = $this->tempPath($localTempFilename);

        if (!Storage::disk('local')->exists($localPath)) {
            throw new RuntimeException("Arquivo temporário {$localTempFilename} não encontrado para upload.");
        }

        // Usamos readStream e writeStream para economizar RAM ao enviar para a AWS/Azure
        $fileStream = Storage::disk('local')->readStream($localPath);
        Storage::disk($this->finalDisk)->writeStream($destinationPath, $fileStream);

        if (is_resource($fileStream)) {
            fclose($fileStream);
        }

        // Limpa o arquivo local final após o upload para a nuvem
        $this->deleteTemp($localTempFilename);
    }

    protected function tempPath(string $filename): string
    {
        return $this->tempDir . '/' . $filename;
    }

    public function existsTemp(string $filename): bool
    {
        return Storage::disk('local')->exists($this->tempPath($filename));
    }
}
