<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use App\Actions\ScrapeCollectionCalendarPage;

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
    public function handle(ScrapeCollectionCalendarPage $scrape)
    {
        collect([
            'https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/thursday-a-collection-calendar/',
            'https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/thursday-b-collection-calendar/',
        ])->each(function ($url) use ($scrape) {
            $scrape($url);

            $this->info("Scraped URL $url successfully");
        });

        return 0;
    }
}
