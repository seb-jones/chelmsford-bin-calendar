<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use App\Spiders\CollectionCalendarSpider;
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
    public function handle()
    {
        $this->line("Application is running in " . config('app.env') . " mode");

        $entries = Roach::collectSpider(CollectionCalendarSpider::class);

        collect($entries)->each(function ($entry) {
            $this->info($entry['title']);

            $entry['items']->each(function ($item) {
                $this->comment("{$item['date']->format('l jS F')}\t{$item['description']}");
            });
        });

        return 0;
    }
}
