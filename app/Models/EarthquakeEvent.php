<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EarthquakeEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'magnitude',
        'status',
        'occurred_at',
        'latitude',
        'longitude',
        'depth',
        'description'
    ];

    protected $casts = [
        'magnitude' => 'float',
        'occurred_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'depth' => 'float'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function getStatusColorAttribute()
    {
        return [
            'warning' => 'warning',
            'danger' => 'danger',
            'normal' => 'success'
        ][$this->status] ?? 'secondary';
    }

    public function getStatusIconAttribute()
    {
        return [
            'warning' => '⚠️',
            'danger' => '🔥',
            'normal' => '✅'
        ][$this->status] ?? 'ℹ️';
    }

    public function getMagnitudeColorAttribute()
    {
        if ($this->magnitude >= 5.0) return 'danger';
        if ($this->magnitude >= 3.0) return 'warning';
        return 'success';
    }

    public function getFormattedOccurredAtAttribute()
    {
        return $this->occurred_at->format('M d, Y H:i:s');
    }

    public function getTimeAgoAttribute()
    {
        return $this->occurred_at->diffForHumans();
    }

    public function hasLocation()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    // Scope for today's events
    public function scopeToday($query)
    {
        return $query->whereDate('occurred_at', today());
    }

    // Scope for recent events
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('occurred_at', '>=', now()->subDays($days));
    }

    // Scope by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope by device
    public function scopeByDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    // Scope by date range
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('occurred_at', [$startDate, $endDate]);
    }

    // Scope by magnitude range
    public function scopeMagnitudeRange($query, $min, $max)
    {
        return $query->whereBetween('magnitude', [$min, $max]);
    }
}
