<?php

use App\DTOs\Calendar;
use App\Spiders\CollectionCalendarSpider;
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

test('calendar title matches h1 in HTML', function () {
    expect(runSpiderAndGetFirstCalendar()->title)
        ->toBe('Thursday A collection calendar');
});

test('calendar first month matches first h2 in HTML', function () {
    expect(runSpiderAndGetFirstCalendar()->firstMonth->format('Y-m'))
        ->toBe('2022-06');
});

test('calendar months match h2s in HTML', function () {
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
