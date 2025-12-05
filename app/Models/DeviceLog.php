<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'status',
        'magnitude',
        'logged_at'
    ];

    protected $casts = [
        'magnitude' => 'float',
        'logged_at' => 'datetime'
    ];

    public $timestamps = false; // Karena tabel tidak memiliki created_at/updated_at

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    // Scope for today's logs
    public function scopeToday($query)
    {
        return $query->whereDate('logged_at', today());
    }

    // Scope for recent logs
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('logged_at', '>=', now()->subDays($days));
    }
}
