<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'role',
        'phone',
        'address',
        'city',
        'country',
        'timezone',
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'language',
        'bio',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean'
        ];
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function getFullAddressAttribute()
    {
        $parts = [];
        if ($this->address) $parts[] = $this->address;
        if ($this->city) $parts[] = $this->city;
        if ($this->country) $parts[] = $this->country;

        return implode(', ', $parts);
    }

    public function getNotificationStatusAttribute()
    {
        $statuses = [];
        if ($this->email_notifications) $statuses[] = 'Email';
        if ($this->sms_notifications) $statuses[] = 'SMS';
        if ($this->push_notifications) $statuses[] = 'Push';

        return empty($statuses) ? 'None' : implode(', ', $statuses);
    }

    public function getLanguageNameAttribute()
    {
        return [
            'en' => 'English',
            'id' => 'Indonesian'
        ][$this->language] ?? 'English';
    }

    public function getTimezoneNameAttribute()
    {
        $timezones = [
            'Asia/Jakarta' => 'WIB (Jakarta)',
            'Asia/Makassar' => 'WITA (Makassar)',
            'Asia/Jayapura' => 'WIT (Jayapura)',
            'UTC' => 'UTC'
        ];

        return $timezones[$this->timezone] ?? $this->timezone;
    }
}
