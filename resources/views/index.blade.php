<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width" />
        <title>Chelmsford Bin Collection Calendar</title>
    </head>
    <body>
        <h1>Chelmsford Collection Calendars</h1>

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
