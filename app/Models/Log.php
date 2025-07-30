<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public function logResponse()
    {
        return $this->hasOne(LogResponse::class);
    }
    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
