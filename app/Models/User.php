<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function monitors()
    {
        return $this->hasMany(Monitor::class);
    }
    public function uptimes()
    {
        return Monitor::where('user_id', $this->id)
            ->select('id', 'created_at', 'uptime_12h', 'uptime_24h', 'uptime_7d', 'uptime_30d');
    }

    public function downtimes()
    {
        return Monitor::where('user_id', $this->id)
            ->select('id', 'created_at', 'downtime_12h', 'downtime_24h', 'downtime_7d', 'downtime_30d');
    }
}
