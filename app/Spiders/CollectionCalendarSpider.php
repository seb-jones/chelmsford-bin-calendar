<?php

namespace App\Spiders;

use App\DTOs\CalendarEntry;
use App\DTOs\Calendar;
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
        )->map(
            fn ($h2) => Carbon::parse($h2)
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
        $urls = $this->context['urls'] ?? [
            "https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/tuesday-a-collection-calendar/",
            "https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/tuesday-b-collection-calendar/",
            "https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/wednesday-a-collection-calendar/",
            "https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/wednesday-b-collection-calendar/",
            "https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/thursday-a-collection-calendar/",
            "https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/thursday-b-collection-calendar/",
            "https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/friday-a-collection-calendar/",
            "https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/friday-b-collection-calendar/",
        ];

        return collect($urls)->map(
            fn ($url) => new Request(
                'GET',
                $url,
                [$this, 'parse'],
                ['verify' => false],
            ),
        )->toArray();
    }
}
