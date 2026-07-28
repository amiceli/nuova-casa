<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// the awesome-newsletters README moves slowly, a monthly read is enough
Schedule::command('newsletters:sync-catalog')
    ->monthlyOn(1, '03:00')
    ->withoutOverlapping();

// the day after the catalog, the logos it added are the ones to look for
Schedule::command('newsletters:sync-logos --limit=50')
    ->monthlyOn(2, '03:00')
    ->withoutOverlapping();

// what the imports left with the fallback icon
Schedule::command('bookmarks:sync-icons --limit=50')
    ->weeklyOn(1, '04:00')
    ->withoutOverlapping();
