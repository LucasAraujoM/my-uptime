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
    /**
     * Ping a host and return the result
     * 
     * @param string $host The host to ping
     * @return array The ping result with success status and time
     */
    private static function pingHost($host)
    {
        $result = ['success' => false, 'time' => 0];
        
        $startTime = microtime(true);
        
        // For Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $cmd = 'ping -n 1 -w 1000 ' . escapeshellarg($host);
            exec($cmd, $output, $returnVar);
            $result['success'] = $returnVar === 0;
        } 
        // For Linux/Unix/MacOS
        else {
            $cmd = 'ping -c 1 -W 1 ' . escapeshellarg($host);
            exec($cmd, $output, $returnVar);
            $result['success'] = $returnVar === 0;
        }
        
        $result['time'] = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        
        return $result;
    }
    public static function checkStatus($monitorId)
    {
        try {
            $monitor = Monitor::where('id', $monitorId)->first();
            
            // Get timeout value from monitor settings or use default
            $timeoutValue = 10; // Default timeout
            if ($monitor->timeout) {
                $timeoutMap = [
                    '0' => 10,  // 10 seconds
                    '1' => 30,  // 30 seconds
                    '2' => 60,  // 1 minute
                    '3' => 300, // 5 minutes
                ];
                $timeoutValue = $timeoutMap[$monitor->timeout] ?? 10;
            }
            
            // Handle different HTTP methods
            $httpMethod = 'get'; // Default method
            if ($monitor->type === 'http' && $monitor->method) {
                $methodMap = [
                    '0' => 'get',
                    '1' => 'post',
                    '2' => 'put',
                    '3' => 'delete',
                ];
                $httpMethod = $methodMap[$monitor->method] ?? 'get';
            }
            
            // Make the request based on the method
            $response = null;
            if ($monitor->type === 'http') {
                $response = Http::timeout($timeoutValue)->$httpMethod($monitor->url);
            } elseif ($monitor->type === 'ping') {
                // For ping type, we use a simple ping implementation
                $pingResult = self::pingHost(parse_url($monitor->url, PHP_URL_HOST));
                
                $log = new ResponseLog();
                $log->monitor_id = $monitor->id;
                $log->status = $pingResult['success'] ? 200 : 500;
                $log->response_time = $pingResult['time'];
                $log->error_message = $pingResult['success'] ? null : 'Host unreachable';
                $log->save();
                
                $log->logResponse()->create([
                    'response_content' => json_encode($pingResult),
                ]);
                
                $monitor->status = $pingResult['success'] ? 'up' : 'down';
                $monitor->save();
                return;
            }
            
            // Create log entry
            $log = new ResponseLog();
            $log->monitor_id = $monitor->id;
            $log->status = $response->status() === 200 ? 'up' : 'down';
            $log->response_time = $response->transferStats->getTransferTime();
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
                
                // Check keyword based on condition
                if ($monitor->keyword && $monitor->condition) {
                    $keywordExists = strpos($response->body(), $monitor->keyword) !== false;
                    
                    switch ($monitor->condition) {
                        case '1': // When keyword exists
                            if (!$keywordExists) {
                                $monitor->status = 'down';
                                $log->error_message = 'Keyword not found';
                                $log->save();
                            }
                            break;
                        case '2': // When keyword not exists
                            if ($keywordExists) {
                                $monitor->status = 'down';
                                $log->error_message = 'Keyword found but should not exist';
                                $log->save();
                            }
                            break;
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
