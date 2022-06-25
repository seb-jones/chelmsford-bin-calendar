<?php

namespace App\Spiders;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;

class CollectionCalendarSpider extends BasicSpider
{
    public function parse(Response $response): \Generator
    {
        $title = $response->filter('h1')->text();

        $items = collect(
            $response->filter('h2 + ul > li')->each(fn ($element) => $element->text())
        )->filter(
            fn ($li) => Str::of($li)
                ->trim()
                ->match('/^(monday|tuesday|wednesday|thursday|friday|saturday|sunday).+:.+$/i')
                ->isNotEmpty()
        )->map(function ($li) {
            // Replace Unicode spaces with ASCII spaces
            $text = preg_replace('/[\pZ\pC]/u', ' ', $li);

            [ $date, $description ] = explode(':', $text);

            return [
                'date' => Carbon::parse($date),
                'description' => trim($description),
            ];
        })->values();

        yield $this->item(compact('title', 'items'));
    }

    /** @return Request[] */
    protected function initialRequests(): array
    {
        return [
            new Request(
                'GET',
                'https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/thursday-a-collection-calendar/',
                [$this, 'parse'],
                ['verify' => config('app.env') !== 'development'],
            ),
        ];
    }
}
