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
        $this->line("Application is running in " . config('app.env') . " mode");

        collect([
            'thursday-a',
            'thursday-b',
        ])->map(function ($slug) use ($scrape) {
            $this->info("Scraping $slug");

            $entries = $scrape(
                "https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/$slug-collection-calendar/"
            )->each(function ($entry) {
                $this->comment("{$entry['date']->format('l jS F')}\t{$entry['text']}");
            });
        });

        return 0;
    }
}
