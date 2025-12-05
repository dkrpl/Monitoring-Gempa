<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log an activity
     *
     * @param string $action
     * @param string $description
     * @param mixed $model
     * @param array $details
     * @return ActivityLog
     */
    public static function logActivity($action, $description, $model = null, $details = [])
    {
        $log = new ActivityLog([
            'user_id' => Auth::id(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'action' => $action,
            'description' => $description,
            'details' => $details
        ]);

        if ($model) {
            $log->model_type = get_class($model);
            $log->model_id = $model->id;
        }

        $log->save();
        return $log;
    }

    /**
     * Log user login
     */
    public static function logLogin($user)
    {
        return self::logActivity('login', 'User logged in', $user, [
            'method' => 'web'
        ]);
    }

    /**
     * Log user logout
     */
    public static function logLogout($user)
    {
        return self::logActivity('logout', 'User logged out', $user);
    }

    /**
     * Log profile update
     */
    public static function logProfileUpdate($user, $changes)
    {
        return self::logActivity('profile_update', 'User updated profile', $user, [
            'changes' => $changes
        ]);
    }

    /**
     * Log settings update
     */
    public static function logSettingsUpdate($user, $settings)
    {
        return self::logActivity('settings_update', 'System settings updated', $user, [
            'settings' => $settings
        ]);
    }

    /**
     * Log device activity
     */
    public static function logDeviceActivity($action, $device, $details = [])
    {
        return self::logActivity("device_{$action}", "Device {$action}: {$device->nama_device}", $device, $details);
    }

    /**
     * Log earthquake event
     */
    public static function logEarthquakeEvent($action, $event, $details = [])
    {
        return self::logActivity("earthquake_{$action}", "Earthquake event {$action}", $event, $details);
    }
}
