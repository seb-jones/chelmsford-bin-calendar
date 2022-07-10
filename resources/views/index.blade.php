<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width" />
        <title>Chelmsford Bin Collection Calendar</title>
    </head>
    <body>
        <h1>Chelmsford Collection Calendars</h1>

        <p>iCal (<code>.ics</code>) files that can be imported into calendar software such as Google Calendar. Includes descriptions of which bins need to go out on each day, and email notifications the night before. These files were created by automatically extracting the data publicly available on the <a href="https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/">Chelmsford City Council website</a>.</p>

        @foreach ($calendarsByMonths as $months => $calendars)
            <h2>{{ $months }}</h2>
            <ul>
                @foreach ($calendars as $calendar)
                    <li>
                        <a href="ics/{{ $calendar->filename }}" download>{{ $calendar->title }}</a>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </body>
</html>
