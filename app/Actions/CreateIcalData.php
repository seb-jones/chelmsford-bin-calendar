<?php

namespace App\Actions;

use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\Date;
use Eluceo\iCal\Domain\ValueObject\SingleDay;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Illuminate\Support\Collection;

class CreateIcalData
{
    public function __invoke($calendar):string
    {
        $events = collect($calendar['items'])->map(function ($item) {
            $event = new Event();

            $event->setSummary($item['description'])
                  ->setOccurrence(
                      new SingleDay(
                          new Date($item['date'])
                      )
                  );

            return $event;
        });

        $calendar = new Calendar($events->toArray());

        return (string)(new CalendarFactory())->createCalendar($calendar);
    }
}
