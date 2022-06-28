<?php

namespace App\Commands;

use App\Actions\CreateIcalData;
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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CreateIcalData $createIcalData)
    {
        $this->line("Application is running in " . config('app.env') . " mode");

        $outputDirectory = 'output';

        if (!File::isDirectory($outputDirectory)) {
            File::makeDirectory($outputDirectory);
            $this->info('Created output directory');
        }

        $calendars = collect(
            Roach::collectSpider(CollectionCalendarSpider::class)
        );

        $this->info('Scraped Calendar Data');

        collect($calendars)
            ->map(function ($calendar) {
                $calendar['filename'] = Str::slug($calendar['title']) . '.ics';

                return $calendar;
            })->each(function ($calendar) use ($createIcalData, $outputDirectory) {
                $this->info("\n{$calendar['title']} {$calendar['months']->first()} to {$calendar['months']->last()}");

                $calendar['items']->each(function ($item) {
                    $this->comment("{$item['date']->format('l jS F')}\t{$item['description']}");
                });

                File::put(
                    base_path("$outputDirectory/{$calendar['filename']}"),
                    $createIcalData($calendar)
                );
            })->tap(function ($calendars) use ($outputDirectory) {
                File::put(
                    "$outputDirectory/index.html",
                    View::make('index', compact('calendars'))
                );
            });

        return 0;
    }
}
