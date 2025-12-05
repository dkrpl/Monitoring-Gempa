<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    protected $casts = [
        'value' => 'string'
    ];

    /**
     * Get a setting value by key with default fallback.
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value.
     */
    public static function setValue($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Check if a setting exists.
     */
    public static function has($key)
    {
        return self::where('key', $key)->exists();
    }

    /**
     * Remove a setting.
     */
    public static function remove($key)
    {
        return self::where('key', $key)->delete();
    }

    /**
     * Get all settings as an associative array.
     */
    public static function getAll()
    {
        return self::all()->pluck('value', 'key')->toArray();
    }

    /**
     * Get boolean setting value.
     */
    public static function getBool($key, $default = false)
    {
        $value = self::getValue($key, $default);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get integer setting value.
     */
    public static function getInt($key, $default = 0)
    {
        $value = self::getValue($key, $default);
        return (int) $value;
    }

    /**
     * Get float setting value.
     */
    public static function getFloat($key, $default = 0.0)
    {
        $value = self::getValue($key, $default);
        return (float) $value;
    }

    /**
     * Get array setting value.
     */
    public static function getArray($key, $default = [])
    {
        $value = self::getValue($key, '[]');
        return json_decode($value, true) ?: $default;
    }
}
