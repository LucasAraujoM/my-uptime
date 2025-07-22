<?php

namespace App\Http\Controllers;

use App\Models\Log as ResponseLog;
use App\Models\Monitor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonitorController extends Controller
{
    public static function checkStatus($monitorId)
    {
        try{
            $monitor = Monitor::where('id', $monitorId)->first();
            $response = Http::timeout(10)->get($monitor->url);
            
            $log = new ResponseLog();
            $log->monitor_id = $monitor->id;
            $log->status = $response->status();
            $log->response_time = $response->transferStats->getTransferTime();
            $log->response_content = $response->body();
            
            if ($response->failed()) {
                $log->error_content = $response->body();
                $monitor->status = 'down';
            }
            if ($response->successful()) {
                $monitor->status = 'up';
            }
            $monitor->save();
            $log->save();
            Log::info($monitor->name . ' - Status ' . strtoupper($monitor->status));
        }catch(Exception $e){
            Log::error($e->getMessage());
        }
    }
}
