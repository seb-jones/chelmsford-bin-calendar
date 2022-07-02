<?php

namespace App\Commands;

use App\Actions\BuildIndexPage;
use App\Traits\OutputsFiles;
use Illuminate\Support\Facades\File;
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
    public function handle(BuildIndexPage $buildIndexPage)
    {
        collect(
            File::files(base_path("$this->outputDirectory/ics"))
        )->tap(function ($files) use ($buildIndexPage) {
            $buildIndexPage($files);
        });

        return 0;
    }
}
