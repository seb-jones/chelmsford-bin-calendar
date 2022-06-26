<?php

namespace App\Commands;

use App\Actions\CreateIcalData;
use App\Spiders\CollectionCalendarSpider;
use Illuminate\Support\Facades\File;
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

        $calendars = collect(
            Roach::collectSpider(CollectionCalendarSpider::class)
        );

        collect($calendars)->each(function ($calendar) use ($createIcalData) {
            $this->info($calendar['title']);

            $calendar['items']->each(function ($item) {
                $this->comment("{$item['date']->format('l jS F')}\t{$item['description']}");
            });

            File::put(
                base_path(Str::slug($calendar['title']) . '.ics'),
                $createIcalData($calendar)
            );
        });

        return 0;
    }
}
