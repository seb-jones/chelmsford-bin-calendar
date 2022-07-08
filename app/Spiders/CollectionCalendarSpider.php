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
    public const H2_MONTH_REGEX = '/^(january|february|march|april|may|june|july|august|september|october|november|december)(.+)(\d\d\d\d)$/i';
    public const LI_MONTH_REGEX = '/^(monday|tuesday|wednesday|thursday|friday|saturday|sunday).+:.+$/i';

    public function parse(Response $response): \Generator
    {
        $title = $response->filter('h1')->text();

        $h2s = collect(
            $response->filter('h2')->each(
                fn ($h2) => (object)[
                    'node' => $h2,
                    'title' => Str::of($this->replaceUnicodeSpacesWithAsciiSpaces($h2->text()))->trim(),
                ]
            )
        )->filter(
            fn ($h2) => $h2->title->match(self::H2_MONTH_REGEX)->isNotEmpty()
        )->map(function ($h2) {
            $h2->date = Carbon::parse($h2->title);

            return $h2;
        });

        $months = $h2s->pluck('date');

        $entries = $h2s->map(function ($h2) {
            return collect(
                $h2->node->nextAll()->first()->children('li')->each(
                    fn ($element) => $this->replaceUnicodeSpacesWithAsciiSpaces($element->text())
                )
            )->map(function ($li) use ($h2) {
                [ $date, $description ] = explode(':', $li);

                $date .= " {$h2->date->year}";

                return new CalendarEntry(Carbon::parse($date), trim($description));
            });
        })->flatten(1);

        $uri = $response->getUri();

        yield $this->item([
            'calendar' => new Calendar(
                $title,
                $months,
                $entries,
                $uri
            )
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
