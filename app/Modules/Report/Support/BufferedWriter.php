<?php

namespace App\Modules\Report\Support;

use App\Modules\Report\Contracts\ReportStorageInterface;

class BufferedWriter
{
    protected string $buffer = '';
    protected int $count = 0;

    public function __construct(
        protected ReportStorageInterface $storage,
        protected string $file,
        protected int $limit = 100
    ) {}

    public function append(string $content): void
    {
        $this->buffer .= $content;
        $this->count++;

        if ($this->count >= $this->limit) {
            $this->flush();
        }
    }

    public function flush(): void
    {
        if ($this->buffer !== '') {
            $this->storage->appendTemp($this->file, $this->buffer);
            $this->buffer = '';
            $this->count = 0;
        }
    }

}
