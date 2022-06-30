<?php

namespace App\Commands;

use App\Actions\CreateIcalData;
use App\DTOs\Calendar;
use App\DTOs\CalendarEntry;
use App\Spiders\CollectionCalendarSpider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use RoachPHP\Roach;

class Scrape extends Command
{
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
    protected $description = 'Scrapes each collection calendar from bins and recycling site';

    protected $outputDirectory = 'output';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CreateIcalData $createIcalData)
    {
        $this->line("Application is running in " . config('app.env') . " mode");

        if (!File::isDirectory($this->outputDirectory)) {
            File::makeDirectory($this->outputDirectory);
            $this->info('Created output directory');
        }

        $scrapedItems = collect(
            Roach::collectSpider(CollectionCalendarSpider::class)
        );

        $this->info('Scraped Calendar Data');

        $scrapedItems
            ->pluck('calendar')
            ->sortBy(
                fn ($calendar) => "{$calendar->months->first()->year} {$calendar->months->first()->month}"
            )
            ->each(function (Calendar $calendar) use ($createIcalData) {
                $this->info("\n$calendar");

                $calendar->entries->each(function (CalendarEntry $entry) {
                    $this->comment($entry);
                });

                $this->outputFile($calendar->filename, $createIcalData($calendar));
            })
            ->tap(function ($calendars) {
                $this->outputFile(
                    'index.html',
                    View::make('index', compact('calendars'))->render()
                );
            });

        return 0;
    }

    protected function outputFile(string $filename, string $content): void
    {
        File::put(base_path("$this->outputDirectory/$filename"), $content);
    }
}
