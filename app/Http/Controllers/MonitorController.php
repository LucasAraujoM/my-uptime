<?php

namespace App\Http\Controllers;

use App\Jobs\AlertMessage;
use App\Models\Log as ResponseLog;
use App\Models\Monitor;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
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
    private static function pingHost(string $host): array
    {
        $result = ['success' => false, 'time' => 0, 'error_message' => null];

        $startTime = microtime(true);

        // For Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $cmd = 'ping -n 1 -w 1000 ' . escapeshellarg($host);
        }
        // For Linux/Unix/MacOS
        else {
            $cmd = 'ping -c 1 -W 1 ' . escapeshellarg($host);
        }

        exec($cmd, $output, $returnVar);

        $result['success'] = $returnVar === 0;
        $result['time'] = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        if (!$result['success']) {
            $result['error_message'] = 'Host unreachable or ping command failed.';
        }

        return $result;
    }

    public static function checkStatus(int $monitorId): void
    {
        try {
            $monitor = Monitor::find($monitorId);

            if (!$monitor) {
                //Log::warning("Monitor with ID {$monitorId} not found.");
                return;
            }

            $log = new ResponseLog();
            $log->monitor_id = $monitor->id;
            $currentMonitorStatus = 'up'; // Assume up until proven otherwise
            $errorMessage = null;
            $responseTime = 0;
            $responseBody = null;

            // Determine timeout value
            $timeoutMap = [
                '0' => 10,  // 10 seconds
                '1' => 30,  // 30 seconds
                '2' => 60,  // 1 minute
                '3' => 300, // 5 minutes
            ];
            $timeoutValue = $timeoutMap[$monitor->timeout] ?? 10;

            if ($monitor->type === 'http') {
                // Determine HTTP method
                $methodMap = [
                    '0' => 'get',
                    '1' => 'post',
                    '2' => 'put',
                    '3' => 'delete',
                ];
                $httpMethod = $methodMap[$monitor->method] ?? 'get';

                try {
                    $response = Http::timeout($timeoutValue)->{$httpMethod}($monitor->url);
                    $responseBody = $response->body();
                    $responseTime = $response->transferStats ? $response->transferStats->getTransferTime() * 1000 : 0;

                    if ($response->failed()) {
                        $currentMonitorStatus = 'down';
                        $errorMessage = "HTTP Request failed with status: {$response->status()}";
                        if ($response->clientError() || $response->serverError()) {
                            $errorMessage .= " Body: {$responseBody}";
                        }
                    } else {
                        // Check keyword based on condition
                        if ($monitor->keyword && $monitor->condition) {
                            $keywordExists = strpos($responseBody, $monitor->keyword) !== false;

                            switch ($monitor->condition) {
                                case '1': // When keyword exists
                                    if (!$keywordExists) {
                                        $currentMonitorStatus = 'down';
                                        $errorMessage = 'Keyword not found in response.';
                                    }
                                    break;
                                case '2': // When keyword not exists
                                    if ($keywordExists) {
                                        $currentMonitorStatus = 'down';
                                        $errorMessage = 'Keyword found but should not exist.';
                                    }
                                    break;
                            }
                        }
                    }
                } catch (ConnectionException $e) {
                    $currentMonitorStatus = 'down';
                    $errorMessage = 'Connection Error: '/*  . $e->getMessage() */ ;
                } catch (RequestException $e) {
                    $currentMonitorStatus = 'down';
                    $errorMessage = 'Request Error: ' /* . $e->getMessage() */ ;
                    if ($e->response) {
                        $responseBody = $e->response->body();
                    }
                }
            } elseif ($monitor->type === 'ping') {
                $pingResult = self::pingHost(parse_url($monitor->url, PHP_URL_HOST));
                $responseTime = $pingResult['time'];
                $responseBody = json_encode($pingResult);

                if (!$pingResult['success']) {
                    $currentMonitorStatus = 'down';
                    $errorMessage = $pingResult['error_message'];
                }
            }

            // Update log entry
            $log->status = $currentMonitorStatus;
            $log->response_time = $responseTime;
            $log->error_message = $errorMessage;
            $log->save();

            // Store response content
            $log->logResponse()->create([
                'response_content' => $responseBody ?: 'No response body captured.',
            ]);

            // Update monitor status
            $monitor->status = $currentMonitorStatus;
            $monitor->save();

            if ($monitor->status == 'down') {
                /* AlertMessage::dispatch('discord', 'singular', $monitor->id); */
            }
            //Log::info($monitor->name . ' - Status ' . strtoupper($monitor->status) . ($errorMessage ? " ({$errorMessage})" : ''));
        } catch (Exception $e) {
            Log::error("Error checking monitor ID {$monitorId}: " . $e->getMessage());
        }
    }
}
