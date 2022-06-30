<?php

namespace App\DTOs;

use \Illuminate\Support\Carbon;
use \Stringable;

class CalendarEntry implements Stringable
{
    public function __construct(public Carbon $date, public string $description)
    {
    }

    public function __toString(): string
    {
        return "{$this->date->format('l jS F')}\t{$this->description}";
    }
}
