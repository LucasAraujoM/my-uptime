<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Body extends Model
{
    protected $fillable = [
        'monitor_id',
        'body',
    ];
    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
