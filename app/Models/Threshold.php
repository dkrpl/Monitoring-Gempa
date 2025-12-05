<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Threshold extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_value',
        'description',
        'color',
        'notification_enabled',
        'notification_message',
        'auto_alert',
        'priority'
    ];

    protected $casts = [
        'min_value' => 'float',
        'notification_enabled' => 'boolean',
        'auto_alert' => 'boolean',
        'priority' => 'integer'
    ];

    protected $attributes = [
        'notification_enabled' => true,
        'auto_alert' => false,
        'priority' => 999
    ];

    public function getStatusColorAttribute()
    {
        return [
            'warning' => 'warning',
            'danger' => 'danger',
            'critical' => 'dark'
        ][$this->description] ?? 'secondary';
    }

    public function getStatusIconAttribute()
    {
        return [
            'warning' => '⚠️',
            'danger' => '🔥',
            'critical' => '💀'
        ][$this->description] ?? '📊';
    }

    public function getNotificationStatusAttribute()
    {
        return $this->notification_enabled ? 'Enabled' : 'Disabled';
    }

    public function getAlertStatusAttribute()
    {
        return $this->auto_alert ? 'Auto-alert' : 'Manual';
    }

    public function getFormattedMinValueAttribute()
    {
        return number_format($this->min_value, 1);
    }

    public function getRangeDescriptionAttribute()
    {
        $nextHigher = self::where('min_value', '>', $this->min_value)
            ->orderBy('min_value', 'asc')
            ->first();

        if ($nextHigher) {
            return $this->formatted_min_value . ' - ' . number_format($nextHigher->min_value, 1);
        }

        return '≥ ' . $this->formatted_min_value;
    }

    // Scope for active notifications
    public function scopeWithNotifications($query)
    {
        return $query->where('notification_enabled', true);
    }

    // Scope for auto alerts
    public function scopeWithAutoAlerts($query)
    {
        return $query->where('auto_alert', true);
    }

    // Scope ordered by priority
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority')->orderBy('min_value');
    }

    // Get next threshold
    public function nextThreshold()
    {
        return self::where('min_value', '>', $this->min_value)
            ->orderBy('min_value', 'asc')
            ->first();
    }

    // Get previous threshold
    public function previousThreshold()
    {
        return self::where('min_value', '<', $this->min_value)
            ->orderBy('min_value', 'desc')
            ->first();
    }
}
