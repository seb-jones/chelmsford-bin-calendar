<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width" />
        <title>Chelmsford Bin Collection Calendar</title>
    </head>
    <body>
        <ul>
            @foreach ($calendarFiles as $calendarFile)
                <li>
                    <a href="{{ $calendarFile->filename }}">{{ $calendarFile->title }}</a>
                </li>
            @endforeach
        </ul>
    </body>
</html>
