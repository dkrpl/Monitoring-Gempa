<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'action',
        'description',
        'details',
        'model_type',
        'model_id'
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    // Helpers
    public function getActionColorAttribute()
    {
        $colors = [
            'login' => 'success',
            'logout' => 'secondary',
            'create' => 'primary',
            'update' => 'warning',
            'delete' => 'danger',
            'read' => 'info',
            'error' => 'danger',
            'warning' => 'warning',
            'info' => 'info'
        ];

        $action = explode('_', $this->action)[0];
        return $colors[$action] ?? 'secondary';
    }

    public function getActionIconAttribute()
    {
        $icons = [
            'login' => 'fas fa-sign-in-alt',
            'logout' => 'fas fa-sign-out-alt',
            'create' => 'fas fa-plus-circle',
            'update' => 'fas fa-edit',
            'delete' => 'fas fa-trash',
            'read' => 'fas fa-eye',
            'profile' => 'fas fa-user',
            'settings' => 'fas fa-cog',
            'device' => 'fas fa-microchip',
            'earthquake' => 'fas fa-earthquake'
        ];

        foreach ($icons as $key => $icon) {
            if (str_contains($this->action, $key) || str_contains($this->description, $key)) {
                return $icon;
            }
        }

        return 'fas fa-history';
    }

    public function getFormattedDetailsAttribute()
    {
        if (!$this->details) return null;

        $details = $this->details;
        $formatted = '';

        foreach ($details as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $formatted .= "<strong>{$key}:</strong> {$value}<br>";
        }

        return $formatted;
    }
}
