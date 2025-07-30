<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Header extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];
    public function log()
    {
        return $this->hasOne(Log::class);
    }
}
