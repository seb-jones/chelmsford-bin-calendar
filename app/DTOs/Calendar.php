<?php

namespace App\DTOs;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Calendar
{
    public string $filename;

    /**
     * @param string $title
     * @param Collection<Carbon> $months
     * @param Collection<CalendarEntry> $entries
     * @param string $uri
     */
    public function __construct(public string $title, public Collection $months, public Collection $entries, public string $uri)
    {
        $this->filename = Str::slug($this->title) . '.ics';
    }

    public function __toString(): string
    {
        return "{$this->title} {$this->months->first()} to {$this->months->last()}";
    }
}
