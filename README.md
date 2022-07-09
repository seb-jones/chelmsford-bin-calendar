# Chelmsford Bin Collection Calendar Import

Simple script that scrapes and parses the bin collection dates from the [Chelmsford City Council website](https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/) and outputs ICS files that can be imported into a Google Calendar.

It is built on the [Laravel Zero](https://github.com/laravel-zero/laravel-zero) framework, and uses [Roach](https://github.com/roach-php/core) for scraping and [iCal](https://github.com/markuspoerschke/ical) for `.ics` file output.
