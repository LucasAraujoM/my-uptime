<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateUptime implements ShouldQueue
{
    use Queueable;

    public $monitorId;
    /**
     * Create a new job instance.
     */
    public function __construct($monitorId)
    {
        $this->monitorId = $monitorId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $monitor = Monitor::find($this->monitorId);
        $monitor->calculateUptime();
    }
}
