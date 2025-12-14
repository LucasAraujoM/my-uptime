<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\LogResponse;
use Illuminate\Console\Command;

class PurgeLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purge-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge logs and log responses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        LogResponse::truncate();
    }
}
