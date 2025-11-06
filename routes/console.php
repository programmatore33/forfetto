<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule demo cleanup every hour
Schedule::command('demo:cleanup --force')->hourly();

// Daily cleanup for safety (in case hourly missed some)
Schedule::command('demo:cleanup --force')->daily();
