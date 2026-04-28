<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'paypal_plan_id',
        'price',
        'interval',
        'monitors_limit',
        'check_interval_seconds',
        'features',
        'is_active',
        'is_popular',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
