<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogResponse extends Model
{
    protected $fillable = [
        'log_id',
        'response_content',
    ];

    public function log()
    {
        return $this->belongsTo(Log::class);
    }
}
