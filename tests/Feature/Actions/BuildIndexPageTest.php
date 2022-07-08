<?php

use App\Actions\BuildIndexPage;
use App\DTOs\Calendar;
use App\DTOs\CalendarEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake(config('filesystems.default'));
});

it('writes an index.html file to output directory', function () {
    createFakeCalendarCollectionAndInvokeAction();

    Storage::assertExists('index.html');
});

it('index.html contains hyperlink to .ics file', function () {
    $calendars = createFakeCalendarCollectionAndInvokeAction();

    expectIndexHtmlContents()->toContain(
        '<a href="' . $calendars->first()->filename
    );
});

function expectIndexHtmlContents()
{
    return expect(Storage::get('index.html'));
}

function createFakeCalendarCollectionAndInvokeAction():Collection
{
    $calendars = createFakeCalendarCollection();

    invokeActionWithCalendars($calendars);

    return $calendars;
}


function createFakeCalendarCollection():Collection
{
    return collect([
        new Calendar(
            'Test Calendar',
            collect([
                now()->startOfMonth(),
                now()->addMonth()->startOfMonth(),
            ]),
            collect(
                new CalendarEntry(
                    now()->startOfMonth()->setDay(7),
                    'Test Entry 1',
                ),
                new CalendarEntry(
                    now()->startOfMonth()->setDay(14),
                    'Test Entry 2',
                ),
            ),
            'http://localhost:8000/test-calendar',
        )
    ]);
}

function invokeActionWithCalendars(Collection $calendars):void
{
    resolve(BuildIndexPage::class)($calendars);
}
