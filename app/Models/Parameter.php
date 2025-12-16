<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    protected $fillable = [
        'monitor_id',
        'key',
        'value',
    ];
    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
