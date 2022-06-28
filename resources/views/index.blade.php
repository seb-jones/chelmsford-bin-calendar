<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width" />
        <title>Chelmsford Bin Collection Calendar</title>
    </head>
    <body>
        <ul>
            @foreach ($calendars as $calendar)
                <li>
                    <a href="{{ $calendar['filename'] }}">{{ $calendar['title'] }}</a>
                </li>
            @endforeach
        </ul>
    </body>
</html>