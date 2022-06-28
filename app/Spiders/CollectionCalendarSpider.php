<?php

namespace App\Spiders;

use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;

class CollectionCalendarSpider extends BasicSpider
{
    public function parse(Response $response): \Generator
    {
        $title = $response->filter('h1')->text();

        $months = collect(CarbonPeriod::create('2022-01-01', '1 month', '2023-01-01'))->map(
            fn ($p) => Str::lower($p->format('F'))
        )->implode("|");

        $h2MonthRegex = '/^(january|february|march|april|may|june|july|august|september|october|november|december)/i';

        $months = collect(
            $response->filter('h2')->each(
                fn ($h2) => Str::of(
                    $this->replaceUnicodeSpacesWithAsciiSpaces($h2->text())
                )->trim()
                 ->match($h2MonthRegex)
            )
        )->filter(
            fn ($h2) => $h2->isNotEmpty()
        );

        $liDayRegex = '/^(monday|tuesday|wednesday|thursday|friday|saturday|sunday).+:.+$/i';

        $items = collect(
            $response->filter('h2 + ul > li')->each(fn ($element) => $element->text())
        )->filter(
            fn ($li) => Str::of($li)->trim()->match($liDayRegex)->isNotEmpty()
        )->map(function ($li) {
            $text = $this->replaceUnicodeSpacesWithAsciiSpaces($li);

            [ $date, $description ] = explode(':', $text);

            return [
                'date' => Carbon::parse($date),
                'description' => trim($description),
            ];
        })->values();

        $uri = $response->getUri();

        yield $this->item(compact('title', 'months', 'items', 'uri'));
    }

    private function replaceUnicodeSpacesWithAsciiSpaces(string $string):string
    {
        return preg_replace('/[\pZ\pC]/u', ' ', $string);
    }

    /** @return Request[] */
    protected function initialRequests(): array
    {
        return collect([
            'tuesday-a',
            'tuesday-b',
            'wednesday-a',
            'wednesday-b',
            'thursday-a',
            'thursday-b',
            'friday-a',
            'friday-b',
        ])->map(
            fn ($slug) => new Request(
                'GET',
                "https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/$slug-collection-calendar/",
                [$this, 'parse'],
                ['verify' => config('app.env') !== 'development'],
            ),
        )->toArray();
    }
}
