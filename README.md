# Chelmsford Bin Collection Calendar Import

Simple script that scrapes and parses the bin collection dates from the [Chelmsford City Council website](https://www.chelmsford.gov.uk/bins-and-recycling/check-your-collection-day/) and outputs ICS files that can be imported into a Google Calendar.

It is built on the [Laravel Zero](https://github.com/laravel-zero/laravel-zero) framework, and uses [Roach](https://github.com/roach-php/core) for scraping and [iCal](https://github.com/markuspoerschke/ical) for `.ics` file output.

## Todo

- [x] [Schedule](https://docs.github.com/en/actions/using-workflows/events-that-trigger-workflows#schedule) workflow to perform scrape at regular interval (e.g. daily? weekly?)
- [x] Order by year/month and then by collection round day
- [x] Make classes for Calendar and Entry
- [x] Structure output sub-directories to naturally sort correctly
- [x] Make action for writing index.html based on existing .ics files, rather than date that was just scraped
- [ ] Output links in html grouped by Year and Month
- [ ] Commit output files after run in CI?
- [ ] Publish on Github pages
