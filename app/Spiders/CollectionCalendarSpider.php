<?php

namespace App\Spiders;

use App\DTOs\CalendarEntry;
use App\DTOs\Calendar;
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

        $entries = collect(
            $response->filter('h2 + ul > li')->each(fn ($element) => $element->text())
        )->filter(
            fn ($li) => Str::of($li)->trim()->match($liDayRegex)->isNotEmpty()
        )->map(function ($li) {
            $text = $this->replaceUnicodeSpacesWithAsciiSpaces($li);

            [ $date, $description ] = explode(':', $text);

            return new CalendarEntry(Carbon::parse($date), trim($description));
        })->values();

        $uri = $response->getUri();

        yield $this->item([
            'calendar' => new Calendar($title, $months, $entries, $uri)
        ]);
    }

    private function replaceUnicodeSpacesWithAsciiSpaces(string $string):string
    {
        return preg_replace('/[\pZ\pC]/u', ' ', $string) ?? '';
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
