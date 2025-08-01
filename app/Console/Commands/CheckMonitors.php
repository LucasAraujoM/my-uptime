<?php

namespace App\Console\Commands;

use App\Models\Monitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckMonitors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:monitors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $monitors = Monitor::all();
        foreach ($monitors as $monitor) {
            $monitor->calculateUptime();
        }
    }
}
