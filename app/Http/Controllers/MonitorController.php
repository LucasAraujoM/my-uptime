<?php

namespace App\Http\Controllers;

use App\Models\Log as ResponseLog;
use App\Models\LogResponse;
use App\Models\Monitor;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonitorController extends Controller
{
    public static function checkStatus($monitorId)
    {
        try {
            $monitor = Monitor::where('id', $monitorId)->first();
            $response = Http::timeout(10)->get($monitor->url);

            $log = new ResponseLog();
            $log->monitor_id = $monitor->id;
            $log->status = $response->status();
            $log->response_time = $response->transferStats->getTransferTime();
            //$log->response_content = $response->body();
            $log->save(); // Save the log first to get an ID
            
            $log->logResponse()->create([
                'response_content' => $response->body(),
            ]);

            if ($response->failed()) {
                $log->error_message = $response->body();
                $log->save();
                $monitor->status = 'down';
            }
            if ($response->successful()) {
                $monitor->status = 'up';
                if ($monitor->keyword) {
                    if (strpos($response->body(), $monitor->keyword) === false) {
                        $monitor->status = 'down';
                        $log->error_message = 'Keyword not found';
                        $log->save();
                    }
                    if (strpos($response->body(), $monitor->keyword) !== false) {
                        $monitor->status = 'up';
                    }
                }
            }
            $monitor->save();
            
            Log::info($monitor->name . ' - Status ' . strtoupper($monitor->status));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
