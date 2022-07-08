<?php

namespace App\Commands;

use App\Actions\BuildIndexPage;
use App\Actions\CreateIcalData;
use App\DTOs\Calendar;
use App\DTOs\CalendarEntry;
use App\Spiders\CollectionCalendarSpider;
use App\Traits\OutputsFiles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use RoachPHP\Roach;

class Scrape extends Command
{
    use OutputsFiles;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'scrape';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Scrapes each collection calendar from bins and recycling site, writes them to .ics files and builds an index page with links';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CreateIcalData $createIcalData, BuildIndexPage $buildIndexPage)
    {
        $this->line("Application is running in " . config('app.env') . " mode");

        if (!File::isDirectory("$this->outputDirectory/ics")) {
            File::makeDirectory("$this->outputDirectory/ics", recursive: true);
            $this->info('Created output directory');
        }

        $scrapedItems = collect(
            Roach::collectSpider(CollectionCalendarSpider::class)
        );

        $this->info('Scraped Calendar Data');

        $scrapedItems
            ->pluck('calendar')
            ->each(function (Calendar $calendar) use ($createIcalData) {
                $this->info("\n$calendar");

                $calendar->entries->each(function (CalendarEntry $entry) {
                    $this->comment($entry);
                });

                $this->outputFile("ics/$calendar->filename", $createIcalData($calendar));
            })
            ->tap(function (Collection $calendars) use ($buildIndexPage) {
                $buildIndexPage($calendars);

                $this->info("\nBuilt index page");
            });

        return 0;
    }
}
