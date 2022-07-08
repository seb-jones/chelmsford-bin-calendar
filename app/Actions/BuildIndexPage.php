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
        $calendars = $calendars->sortBy(
            fn ($calendar) => "{$calendar->firstMonth->year} {$calendar->firstMonth->month} {$calendar->day->format('N')} $calendar->title"
        );

        Storage::put('index.html', View::make('index', compact('calendars'))->render());
    }
}
