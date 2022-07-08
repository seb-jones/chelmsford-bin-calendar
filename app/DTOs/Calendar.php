<?php

namespace App\DTOs;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Calendar
{
    public string $filename;
    public Carbon $firstMonth;
    public Carbon $lastMonth;
    public Carbon $day;

    /**
     * @param string $title
     * @param Collection<Carbon> $months
     * @param Collection<CalendarEntry> $entries
     * @param string $uri
     */
    public function __construct(public string $title, public Collection $months, public Collection $entries, public string $uri)
    {
        $this->firstMonth = $months->first();
        $this->lastMonth = $months->last();

        $this->day = Carbon::parse(
            Str::of($title)->match(
                '/^(monday|tuesday|wednesday|thursday|friday|saturday|sunday)/i'
            )
        );

        $this->filename = Str::slug($title) . ".ics";
    }

    public function __toString(): string
    {
        return "{$this->title} {$this->firstMonth->format('F')} to {$this->lastMonth->format('F')}";
    }
}
