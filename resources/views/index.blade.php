<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width" />
        <title>Chelmsford Bin Collection Calendar</title>
    </head>
    <body>
        <h1>Chelmsford Collection Calendars</h1>

        <p><code>.ics</code> files that can be imported into calendar software such as Google Calendar. Includes email notifications the night before.</p>

        @foreach ($calendarsByMonths as $months => $calendars)
            <h2>{{ $months }}</h2>
            <ul>
                @foreach ($calendars as $calendar)
                    <li>
                        <a href="{{ $calendar->filename }}">{{ $calendar->title }}</a>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </body>
</html>
