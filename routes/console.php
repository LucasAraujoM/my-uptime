<?php

use App\Jobs\CheckMonitor;
use App\Models\Monitor;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $monitors = Monitor::all();
    foreach ($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
        break;
    }
})->everyFiveSeconds();
