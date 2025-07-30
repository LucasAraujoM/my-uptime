<?php

use App\Jobs\CheckMonitor;
use App\Models\Monitor;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_30_seconds')->get();
    foreach ($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->everyThirtySeconds();

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_1_minute')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->everyMinute();

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_5_minutes')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->everyFiveMinutes();

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_15_minutes')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->everyFifteenMinutes();

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_30_minutes')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->everyThirtyMinutes();

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_1_hour')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->hourly();

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_2_hours')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->everyTwoHours();

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_2_hours')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->everyTwoHours();

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_3_hours')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->everyThreeHours();

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_4_hours')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->everyFourHours();

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_5_hours')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->everyOddHour(5);

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_6_hours')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->everySixHours();

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_12_hours')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->everyOddHour(12);

Schedule::call(function () {
    $monitors = Monitor::where('interval', 'every_24_hours')->get();
    foreach($monitors as $monitor) {
        dispatch(new CheckMonitor($monitor->id));
    }
})->daily();

Schedule::command('logs:purge-responses')->dailyAt('03:00');

Schedule::call(function () {
    $monitors = Monitor::all();
    foreach($monitors as $monitor) {
        $monitor->calculateUptime();
    }
})->hourly();
