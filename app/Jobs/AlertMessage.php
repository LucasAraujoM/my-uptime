<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Log;

class AlertMessage implements ShouldQueue
{
    use Queueable;

    public $method;
    public $type;
    public $monitorId;

    /**
     * Create a new job instance.
     */
    public function __construct($method, $type, $monitorId)
    {
        $this->type = $type;
        $this->method = $method;
        $this->monitorId = $monitorId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->method === 'discord') {
            if ($this->type == 'singular') {
                $monitor = Monitor::find($this->monitorId);

                if (!$monitor) {
                    return;
                }

                $webhookUrl = env('DISCORD_WEBHOOK');
                if (!$webhookUrl) {
                    return;
                }
                //monitor down
                $embed = [
                    'title' => 'Monitor Down: ' . $monitor->name,
                    'description' => "The monitor for **{$monitor->name}** ({$monitor->url}) is currently down. Link to monitor: " . route('edit-monitor', $monitor->id),
                    'color' => 15158332, // Red
                    'fields' => [
                        [
                            'name' => 'Time',
                            'value' => now()->toDateTimeString(),
                            'inline' => true
                        ],
                        [
                            'name' => 'Status',
                            'value' => 'DOWN',
                            'inline' => true
                        ]
                    ],
                    'timestamp' => now()->toIso8601String()
                ];

                \Illuminate\Support\Facades\Http::post($webhookUrl, [
                    'embeds' => [$embed]
                ]);
            } else {
                $users = User::all();
                foreach ($users as $user) {
                    $monitors = Monitor::where('user_id', $user->id)->where('status', 'up')->count();
                    Log::info('User ' . $user->id . ' has ' . $monitors . ' monitors');
                    $embed = [
                        'title' => 'All monitors are up',
                        'description' => "All monitors for **{$user->name}** are currently up.",
                        'color' => 65280, // Green
                        'fields' => [
                            [
                                'name' => 'Time',
                                'value' => now()->toDateTimeString(),
                                'inline' => true
                            ],
                            [
                                'name' => 'Status',
                                'value' => 'UP',
                                'inline' => true
                            ]
                        ],
                        'timestamp' => now()->toIso8601String()
                    ];

                    /* \Illuminate\Support\Facades\Http::post($webhookUrl, [
                        'embeds' => [$embed]
                    ]); */
                }
            }
        }
    }
}
