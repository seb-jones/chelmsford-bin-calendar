<?php

use App\DTOs\Calendar;
use App\Spiders\CollectionCalendarSpider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use RoachPHP\Roach;

$serverProcess = null;

beforeAll(function () {
    global $serverProcess;
    $serverProcess = proc_open('cd resources/html && php -S localhost:8123', [], $pipes);
});

afterAll(function () {
    global $serverProcess;
    proc_terminate($serverProcess);
});

it('returns one item', function () {
    expect(runSpiderAndGetCalendars())->toHaveLength(1);
});

it('returns a calendar object', function () {
    expect(runSpiderAndGetFirstCalendar())
        ->toBeInstanceOf(Calendar::class);
});

test('calendar title matches H1 in HTML', function () {
    expect(runSpiderAndGetFirstCalendar()->title)
        ->toBe('Thursday A collection calendar');
});

test('calendar first month matches first H2 in HTML', function () {
    expect(runSpiderAndGetFirstCalendar()->firstMonth->format('Y-m'))
        ->toBe('2022-06');
});

test('calendar months match H2s in HTML', function () {
    expect(
        runSpiderAndGetFirstCalendar()
            ->months
            ->map(fn ($month) => $month->format('Y-m'))
            ->toArray()
    )->toMatchArray([
        '2022-06',
        '2022-07',
        '2022-08',
        '2022-09',
        '2022-10',
    ]);
});

test('calendar entries has length equal to the number of relevant LIs in HTML', function () {
    expect(
        runSpiderAndGetFirstCalendar()
            ->entries
            ->count()
    )->toBe(22);
});


test('calendar entries match dates in HTML', function () {
    $this->travelTo('2020-01-01');

    expect(
        runSpiderAndGetFirstCalendar()
            ->entries
            ->map(fn ($entry) => $entry->date->format('Y-m-d'))
    )->toMatchArray([
        '2022-06-07',
        '2022-06-11',
        '2022-06-17',
        '2022-06-23',
        '2022-06-30',
        '2022-07-07',
        '2022-07-14',
        '2022-07-21',
        '2022-07-28',
        '2022-08-04',
        '2022-08-11',
        '2022-08-18',
        '2022-08-25',
        '2022-09-01',
        '2022-09-08',
        '2022-09-15',
        '2022-09-22',
        '2022-09-29',
        '2022-10-06',
        '2022-10-13',
        '2022-10-20',
        '2022-10-27',
    ]);
});

function runSpiderAndGetFirstCalendar():Calendar
{
    return runSpiderAndGetCalendars()->first();
}

function runSpiderAndGetCalendars():Collection
{
    return collect(
        Roach::collectSpider(
            CollectionCalendarSpider::class,
            context: [
                'urls' => [ 'http://localhost:8123/' ]
            ]
        )
    )->pluck('calendar');
}
