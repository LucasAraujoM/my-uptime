<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Monitor extends Model
{
    protected $fillable = [
        'name',
        'url',
        'keyword',
        'type',
        'uptime_12h',
        'uptime_24h',
        'uptime_7d',
        'uptime_30d'
    ];
    public function logs()
    {
        return $this->hasMany(Log::class);
    }
    public function headers()
    {
        return $this->hasMany(Header::class);
    }
    public function pause()
    {
        if($this->status == 'paused'){
            $this->status = 'pending';
            $this->save();
        }else{
            $this->status = 'paused';
            $this->save();
        }
    }
    
    public function calculateUptime()
    {
        if(Cache::has('monitor_'.$this->id.'_12h_' . $this->user_id)){
            $this->uptime_12h = Cache::get('monitor_'.$this->id.'_12h_' . $this->user_id);
        }else{
            $this->uptime_12h = $this->calculateUptimeForPeriod(12);
            Cache::put('monitor_'.$this->id.'_12h_' . $this->user_id, $this->uptime_12h, now()->addHours(12));
        }
        if(Cache::has('monitor_'.$this->id.'_24h_' . $this->user_id)){
            $this->uptime_24h = Cache::get('monitor_'.$this->id.'_24h_' . $this->user_id);
        }else{
            $this->uptime_24h = $this->calculateUptimeForPeriod(24);
            Cache::put('monitor_'.$this->id.'_24h', $this->uptime_24h, now()->addHours(24));
        }
        if(Cache::has('monitor_'.$this->id.'_7d_' . $this->user_id)){
            $this->uptime_7d = Cache::get('monitor_'.$this->id.'_7d_' . $this->user_id);
        }else{
            $this->uptime_7d = $this->calculateUptimeForPeriod(24 * 7);
            Cache::put('monitor_'.$this->id.'_7d_' . $this->user_id, $this->uptime_7d, now()->addHours(24 * 7));
        }
        if(Cache::has('monitor_'.$this->id.'_30d_' . $this->user_id)){
            $this->uptime_30d = Cache::get('monitor_'.$this->id.'_30d_' . $this->user_id);
        }else{
            $this->uptime_30d = $this->calculateUptimeForPeriod(24 * 30);
            Cache::put('monitor_'.$this->id.'_30d_' . $this->user_id, $this->uptime_30d, now()->addHours(24 * 30));
        }
        $this->save();
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
            ->where(function ($query) {
                $query->whereRaw('CAST(status AS INTEGER) >= 200 AND CAST(status AS INTEGER) <= 299');
            })
            ->count();
            
        return ($successfulLogs / $totalLogs) * 100;
    }
}
