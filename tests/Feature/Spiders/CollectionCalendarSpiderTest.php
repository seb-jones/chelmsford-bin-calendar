<?php

use App\Spiders\CollectionCalendarSpider;
use RoachPHP\Roach;

$serverProcess = null;

beforeAll(function () {
    global $serverProcess;
    $serverProcess = proc_open('cd resources/html && php -S localhost:8123', [], $pipes);
});

it('scrapes an html page', function () {
    $scrapedItems = Roach::collectSpider(
        CollectionCalendarSpider::class,
        context: [
            'urls' => [ 'http://localhost:8123/' ]
        ]
    );
});

afterAll(function () {
    global $serverProcess;
    proc_terminate($serverProcess);
});
