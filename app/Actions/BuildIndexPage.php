<?php

namespace App\Actions;

use App\Traits\OutputsFiles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class BuildIndexPage
{
    use OutputsFiles;

    public function __invoke(Collection $files)
    {
        $files->map(function ($file) {
            preg_match(
                '/^(\d\d\d\d-\d\d)-to-(\d\d\d\d-\d\d)-(\d)-(.+)\.ics/',
                $file->getFilename(),
                $matches,
            );

            return (object)[
                'path' => "ics/{$file->getFilename()}",
                'title' => Str::of($matches[4])->replace('-', ' ')->title(),
            ];
        })->tap(function ($calendarFiles) {
            $this->outputFile(
                'index.html',
                View::make('index', compact('calendarFiles'))->render()
            );
        });
    }
}
