<?php

namespace App\Models;

use App\Http\Controllers\MonitorController;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Log as Logger;

class Monitor extends Model
{
    protected $fillable = [
        'name',
        'url',
        'keyword',
        'type',
        'method',
        'condition',
        'interval',
        'timeout',
        'status',
        'uptime_12h',
        'uptime_24h',
        'uptime_7d',
        'uptime_30d',
        'downtime_12h',
        'downtime_24h',
        'downtime_7d',
        'downtime_30d'
    ];
    public function isPaused()
    {
        return $this->status == 'paused';
    }
    public function logs()
    {
        return $this->hasMany(Log::class);
    }
    public function headers()
    {
        return $this->hasMany(Header::class);
    }
    public function parameters()
    {
        return $this->hasMany(Parameter::class);
    }
    public function body()
    {
        return $this->hasMany(Body::class);
    }
    public function pause()
    {
        if ($this->status == 'paused') {
            $this->status = 'pending';
            $this->save();
            MonitorController::checkStatus($this->id);
        } else {
            $this->status = 'paused';
            $this->save();
        }
    }


    public function calculateUptime()
    {
        // Define all periods to calculate (in hours)
        $periods = [
            '12h' => 12,
            '24h' => 24,
            '7d' => 24 * 7,
            '30d' => 24 * 30,
        ];

        foreach ($periods as $periodKey => $hours) {
            $this->calculateUptimeAndDowntimeForPeriod($periodKey, $hours);
        }

        $this->save();
    }

    /**
     * Calculate uptime and downtime percentages for a specific period
     * 
     * @param string $periodKey The period identifier (e.g., '12h', '24h', '7d', '30d')
     * @param int $hours Number of hours for the period
     * @return void
     */
    protected function calculateUptimeAndDowntimeForPeriod(string $periodKey, int $hours): void
    {
        $uptimeField = "uptime_{$periodKey}";
        $downtimeField = "downtime_{$periodKey}";

        $uptimePercentage = $this->calculateUptimeForPeriod($hours);
        $this->$uptimeField = $uptimePercentage;

        $downtimePercentage = $this->calculateDowntimeForPeriod($hours);
        $this->$downtimeField = $downtimePercentage;
    }

    protected function calculateUptimeForPeriod($hours)
    {
        $periodStart = now()->subHours($hours);

        $totalLogs = $this->logs()
            ->where('created_at', '>=', $periodStart)
            ->count();
        if ($totalLogs === 0) {
            return 0;
        }

        $successfulLogs = $this->logs()
            ->where('created_at', '>=', $periodStart)
            ->where('status', 'up')
            ->count();
        return ($successfulLogs / $totalLogs) * 100;
    }

    /**
     * Calculate downtime percentage for a specific period
     * 
     * @param int $hours Number of hours for the period
     * @return float Downtime percentage (0-100)
     */
    protected function calculateDowntimeForPeriod(int $hours): float
    {
        $periodStart = now()->subHours($hours);

        $totalLogs = $this->logs()
            ->where('created_at', '>=', $periodStart)
            ->count();

        if ($totalLogs === 0) {
            return 0;
        }

        $failedLogs = $this->logs()
            ->where('created_at', '>=', $periodStart)
            ->where('status', 'down')
            ->count();

        return ($failedLogs / $totalLogs) * 100;
    }
}
