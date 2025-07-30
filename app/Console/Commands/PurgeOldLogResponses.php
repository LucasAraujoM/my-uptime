<?php

namespace App\Console\Commands;

use App\Models\LogResponse;
use Illuminate\Console\Command;

class PurgeOldLogResponses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:purge-responses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete response logs older than many days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = now()->subDayS(7);

        $quantity = LogResponse::where('created_at', '<', $limit)->delete();

        $this->info("Deleted $quantity log responses older than 7 days.");
    }
}
