<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait OutputsFiles
{
    protected string $outputDirectory = 'output';

    protected function outputFile(string $filename, string $content): void
    {
        File::put(base_path("$this->outputDirectory/$filename"), $content);
    }
}
