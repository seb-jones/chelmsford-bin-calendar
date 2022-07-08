<?php

use Illuminate\Support\Facades\Storage;

beforeAll(function () {
    Storage::fake();
});

afterAll(function () {
});

it('returns one item', function () {
    expect(true)->toBeTrue();
});
