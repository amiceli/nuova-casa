<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('newsletters:sync-catalog')
    ->weeklyOn(1, '03:00')
    ->withoutOverlapping();

Schedule::command('newsletters:sync-logos --limit=50')
    ->hourly()
    ->withoutOverlapping();
