<?php

namespace App\Jobs;

use App\Http\Controllers\MonitorController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckMonitor implements ShouldQueue
{
    use Queueable;
    public $monitorId = '';
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
        MonitorController::checkStatus($this->monitorId);
    }
}
