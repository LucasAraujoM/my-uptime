<?php

use App\Jobs\CalculateUptime;
use App\Jobs\CheckMonitor;
use App\Models\Monitor;
use Illuminate\Support\Facades\Schedule;

// Map of interval IDs to Laravel schedule methods
$monitorSchedules = [
    '0' => 'everyThirtySeconds',
    '1' => 'everyMinute',
    '2' => 'everyFiveMinutes',
    '3' => 'everyTenMinutes',
    '4' => 'everyFifteenMinutes',
    '5' => 'everyThirtyMinutes',
    '6' => 'hourly',
    '7' => 'everyTwoHours',
    '8' => 'everyThreeHours',
    '9' => 'everyFourHours',
    '10' => 'everyFiveHours',
    '11' => 'everySixHours',
    '12' => 'everyTwelveHours',
    '13' => 'daily',
];

// Schedule monitor checks based on their intervals
foreach ($monitorSchedules as $intervalId => $scheduleMethod) {
    $schedule = Schedule::call(function () use ($intervalId) {
        $monitors = Monitor::where('interval', $intervalId)
            ->where('status', '!=', 'paused')
            ->get();
        foreach ($monitors as $monitor) {
            CheckMonitor::dispatch($monitor->id);
        }
    });

    // Apply the appropriate schedule method
    if ($scheduleMethod === 'everyFiveHours') {
        // everyFiveHours doesn't exist in Laravel, use cron instead
        $schedule->cron('0 */5 * * *');
    } elseif ($scheduleMethod === 'everyTwelveHours') {
        // everyTwelveHours doesn't exist in Laravel, use cron instead
        $schedule->cron('0 */12 * * *');
    } else {
        $schedule->$scheduleMethod();
    }
}

// Purge old response logs daily
Schedule::command('logs:purge-responses')->dailyAt('03:00');

// Calculate uptime statistics hourly
Schedule::call(function () {
    $monitors = Monitor::all();
    foreach ($monitors as $monitor) {
        CalculateUptime::dispatch($monitor->id);
    }
    Log::info("Uptime calculated for: " . $monitors->count() . " monitors");
})->everyMinute();