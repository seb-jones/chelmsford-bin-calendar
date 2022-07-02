<?php

namespace App\Commands;

use App\Traits\OutputsFiles;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class Build extends Command
{
    use OutputsFiles;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Builds an HTML file with links to all the .ics files from the scrape';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $calendarFiles = collect(
            File::files(base_path("$this->outputDirectory/ics"))
        )->map(function ($file) {
            preg_match(
                '/^(\d\d\d\d-\d\d)-to-(\d\d\d\d-\d\d)-(\d)-(.+)\.ics/',
                $file->getFilename(),
                $matches,
            );

            return (object)[
                'filename' => $file->getFilename(),
                'title' => Str::of($matches[4])->replace('-', ' ')->title(),
            ];
        });

        $this->outputFile(
            'index.html',
            View::make('index', compact('calendarFiles'))->render()
        );

        return 0;
    }
}
