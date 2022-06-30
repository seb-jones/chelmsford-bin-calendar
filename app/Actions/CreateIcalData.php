<?php

namespace App\Actions;

use DateInterval;
use App\DTOs\CalendarEntry;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\Alarm;
use Eluceo\iCal\Domain\ValueObject\Alarm\EmailAction;
use Eluceo\iCal\Domain\ValueObject\Alarm\AbsoluteDateTimeTrigger;
use Eluceo\iCal\Domain\ValueObject\Date;
use Eluceo\iCal\Domain\ValueObject\SingleDay;
use Eluceo\iCal\Domain\ValueObject\Timestamp;
use Eluceo\iCal\Domain\ValueObject\Uri;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Illuminate\Support\Collection;
use RoachPHP\ItemPipeline\Item;

class CreateIcalData
{
    public function __invoke(Item $calendar):string
    {
        $events = collect($calendar['entries'])->map(function (CalendarEntry $entry) use ($calendar) {
            $event = new Event();

            $event->setSummary('Bins')
                  ->setDescription($entry->description)
                  ->setUrl(
                      new Uri($calendar['uri'])
                  )
                  ->setOccurrence(
                      new SingleDay(
                          new Date($entry->date)
                      )
                  )
                  ->addAlarm(
                      new Alarm(
                          new EmailAction('Alarm notification', 'This is an event reminder'),
                          new AbsoluteDateTimeTrigger(
                              new Timestamp(
                                  $entry->date->clone()->subDay()->setTime(18, 30)
                              )
                          )
                      )
                  );

            return $event;
        });

        $calendar = new Calendar($events->toArray());

        return (string)(new CalendarFactory())->createCalendar($calendar);
    }
}
