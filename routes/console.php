<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Daily pipeline: ingest all sources, re-score, auto-tailor high matches.
// Chained in one closure so order is guaranteed and a slow source can't
// overlap the scoring step.
Schedule::call(function () {
    Artisan::call('ingest:larajobs');
    Artisan::call('ingest:hn');
    Artisan::call('ingest:wwr');
    Artisan::call('ingest:remoteok');
    Artisan::call('score:run', ['--top' => 0]);
    Artisan::call('tailor:auto');
})->name('daily-pipeline')->dailyAt('07:00')->withoutOverlapping();
