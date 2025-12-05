<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'nama_device',
        'lokasi',
        'status',
        'last_seen'
    ];

    protected $casts = [
        'last_seen' => 'datetime'
    ];

    protected $appends = [
        'event_count',
        'avg_magnitude',
        'max_magnitude',
        'last_event_at'
    ];

    public function earthquakeEvents()
    {
        return $this->hasMany(EarthquakeEvent::class);
    }

    public function logs()
    {
        return $this->hasMany(DeviceLog::class);
    }

    // Get latest log
    public function latestLog()
    {
        return $this->hasOne(DeviceLog::class)->latest('logged_at');
    }

    // Get latest earthquake event
    public function latestEarthquakeEvent()
    {
        return $this->hasOne(EarthquakeEvent::class)->latest('occurred_at');
    }

    public function getStatusColorAttribute()
    {
        return [
            'aktif' => 'success',
            'nonaktif' => 'danger'
        ][$this->status] ?? 'secondary';
    }

    // Count events by status
    public function countEventsByStatus($status)
    {
        return $this->earthquakeEvents()->where('status', $status)->count();
    }

    // Get today's logs count
    public function getTodayLogsCountAttribute()
    {
        return $this->logs()->whereDate('logged_at', today())->count();
    }

    // Accessor for event count
    public function getEventCountAttribute()
    {
        if (isset($this->attributes['event_count'])) {
            return $this->attributes['event_count'];
        }
        return $this->earthquakeEvents()->count();
    }

    // Accessor for average magnitude
    public function getAvgMagnitudeAttribute()
    {
        if (isset($this->attributes['avg_magnitude'])) {
            return round($this->attributes['avg_magnitude'], 2);
        }
        return round($this->earthquakeEvents()->avg('magnitude') ?? 0, 2);
    }

    // Accessor for max magnitude
    public function getMaxMagnitudeAttribute()
    {
        if (isset($this->attributes['max_magnitude'])) {
            return round($this->attributes['max_magnitude'], 2);
        }
        return round($this->earthquakeEvents()->max('magnitude') ?? 0, 2);
    }

    // Accessor for last event at
    public function getLastEventAtAttribute()
    {
        if (isset($this->attributes['last_event'])) {
            return $this->attributes['last_event'];
        }
        return $this->earthquakeEvents()->latest('occurred_at')->first()->occurred_at ?? null;
    }

}
