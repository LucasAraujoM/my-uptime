<?php

namespace Database\Seeders;

use App\Models\Monitor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitorSeeder extends Seeder
{
    protected $count = 100;
    protected $intervals = [
        'every_30_seconds',
        'every_1_minute',
        'every_5_minutes',
        'every_15_minutes',
        'every_30_minutes',
        'every_1_hour',
        'every_2_hours',
        'every_24_hours',
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < $this->count; $i++) {
            Log::info('Monitor-' . $i);
            DB::table('monitors')->insert([
                'name' => 'Monitor-' . $i,
                'url' => 'http://jsonplaceholder.typicode.com/todos/1',
                'interval' => $this->intervals[rand(0, count($this->intervals) - 1)],
                'type' => 'GET',
            ]);
        }
    }
}
