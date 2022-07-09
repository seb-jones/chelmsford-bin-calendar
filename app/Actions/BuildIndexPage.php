<?php

namespace App\Actions;

use App\DTOs\Calendar;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class BuildIndexPage
{
    /**
     * @param Collection<Calendar> $calendars
     */
    public function __invoke(Collection $calendars)
    {
        $calendarsByMonths = $calendars->sortBy(
            fn ($calendar) => "{$calendar->firstMonth->year} {$calendar->firstMonth->month} {$calendar->day->format('N')} $calendar->title"
        )->groupBy(
            fn ($calendar) => "{$calendar->firstMonth->format('F Y')} to {$calendar->lastMonth->format('F Y')}"
        );

        Storage::put(
            'index.html',
            View::make('index', compact('calendarsByMonths'))->render(),
        );
    }
}
