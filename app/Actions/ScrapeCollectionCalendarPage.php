<?php

namespace App\Actions;

use DiDom\Document;
use DiDom\Element;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ScrapeCollectionCalendarPage
{
    private string $monthOptions;

    public function __construct()
    {
        $this->monthOptions = collect(
            now()->startOfYear()->toPeriod(now()->endOfYear(), '1 month')
        )->map(fn($dt) => Str::lower($dt->format('F')))
         ->join('|');

        Log::info("ScrapeCollectionCalendarPage::__construct: generated month options '$this->monthOptions'");
    }

    public function __invoke(string $url)
    {
        Log::info("ScrapeCollectionCalendarPage::__invoke: GET $url");

        $response = Http::withOptions([
            'verify' => config('app.env') !== 'development',
        ])->get($url)->throw();

        Log::info("ScrapeCollectionCalendarPage::__invoke: GET $url responded with code {$response->status()}");

        $document = new Document($response->body());

        $calendarEntries = collect($document->find('ul li'))
            ->filter(
                fn ($li) => Str::of($li->text())
                    ->trim()
                    ->match('/^(monday|tuesday|wednesday|thursday|friday|saturday|sunday).+:/i')
                    ->isNotEmpty()
            )
            ->map(function ($li) {
                [ $date, $text ] = explode(
                    ':',
                    $this->replaceUnicodeSpacesWithAsciiSpaces($li->text())
                );

                return [
                    'date' => Carbon::parse($date)->toDateTimeString(),
                    'text' => trim($text),
                ];
            });

        Log::info("ScrapeCollectionCalendarPage::__invoke: Parsed {$calendarEntries->count()} items from $url");
    }

    private function replaceUnicodeSpacesWithAsciiSpaces(string $s):string
    {
        return preg_replace('/[\pZ\pC]/u', ' ', $s);
    }
}
