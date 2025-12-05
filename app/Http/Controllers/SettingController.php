<?php

namespace App\Http\Controllers;

use App\Models\Threshold;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Display system settings.
     */
    public function index()
    {
        $thresholds = Threshold::orderBy('min_value')->get();
        $settings = $this->getSystemSettings();

        return view('settings.index', compact('thresholds', 'settings'));
    }

    /**
     * Update thresholds.
     */
    public function updateThresholds(Request $request)
    {
        $request->validate([
            'thresholds' => ['required', 'array'],
            'thresholds.*.id' => ['required', 'exists:thresholds,id'],
            'thresholds.*.min_value' => ['required', 'numeric', 'min:0', 'max:10'],
            'thresholds.*.description' => ['required', 'string', 'max:255'],
        ]);

        foreach ($request->thresholds as $thresholdData) {
            Threshold::find($thresholdData['id'])->update([
                'min_value' => $thresholdData['min_value'],
                'description' => $thresholdData['description']
            ]);
        }

        // Clear cache
        Cache::forget('system_thresholds');

        return redirect()->route('settings.index')
            ->with('success', 'Threshold settings updated successfully.');
    }

    /**
     * Update system settings.
     */
    public function updateSystemSettings(Request $request)
    {
        $validated = $request->validate([
            'system_name' => ['required', 'string', 'max:255'],
            'system_email' => ['required', 'email', 'max:255'],
            'alert_email' => ['required', 'email', 'max:255'],
            'alert_sms' => ['nullable', 'string', 'max:20'],
            'data_retention_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'auto_cleanup' => ['required', 'boolean'],
            'enable_email_alerts' => ['required', 'boolean'],
            'enable_sms_alerts' => ['required', 'boolean'],
            'maintenance_mode' => ['required', 'boolean'],
            'timezone' => ['required', 'string', 'timezone'],
            'language' => ['required', 'string', 'in:en,id'],
            'map_provider' => ['required', 'string', 'in:google,openstreetmap,mapbox'],
            'map_api_key' => ['nullable', 'string', 'max:255'],
            'refresh_interval' => ['required', 'integer', 'min:5', 'max:300'],
            'max_log_size' => ['required', 'integer', 'min:10', 'max:1000'],
            'backup_enabled' => ['required', 'boolean'],
            'backup_frequency' => ['required', 'string', 'in:daily,weekly,monthly'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Clear cache
        Cache::forget('system_settings');

        return redirect()->route('settings.index')
            ->with('success', 'System settings updated successfully.');
    }

    /**
     * Update notification settings.
     */
    public function updateNotificationSettings(Request $request)
    {
        $validated = $request->validate([
            'notify_warning' => ['required', 'boolean'],
            'notify_danger' => ['required', 'boolean'],
            'notify_device_offline' => ['required', 'boolean'],
            'notify_device_online' => ['required', 'boolean'],
            'email_template_warning' => ['nullable', 'string', 'max:5000'],
            'email_template_danger' => ['nullable', 'string', 'max:5000'],
            'sms_template_warning' => ['nullable', 'string', 'max:500'],
            'sms_template_danger' => ['nullable', 'string', 'max:500'],
            'push_notifications' => ['required', 'boolean'],
            'slack_webhook' => ['nullable', 'url', 'max:255'],
            'telegram_bot_token' => ['nullable', 'string', 'max:255'],
            'telegram_chat_id' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Clear cache
        Cache::forget('notification_settings');

        return redirect()->route('settings.index')
            ->with('success', 'Notification settings updated successfully.');
    }

    /**
     * Update security settings.
     */
    public function updateSecuritySettings(Request $request)
    {
        $validated = $request->validate([
            'require_2fa' => ['required', 'boolean'],
            'session_timeout' => ['required', 'integer', 'min:5', 'max:480'],
            'max_login_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'password_expiry_days' => ['required', 'integer', 'min:0', 'max:365'],
            'ip_whitelist' => ['nullable', 'string', 'max:1000'],
            'api_rate_limit' => ['required', 'integer', 'min:10, max:1000'],
            'enable_audit_log' => ['required', 'boolean'],
            'data_encryption' => ['required', 'boolean'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Clear cache
        Cache::forget('security_settings');

        return redirect()->route('settings.index')
            ->with('success', 'Security settings updated successfully.');
    }

    /**
     * Get all system settings.
     */
    private function getSystemSettings()
    {
        return Cache::remember('system_settings', 3600, function () {
            $settings = Setting::all()->pluck('value', 'key')->toArray();

            // Default values
            $defaults = [
                'system_name' => 'EQMonitor - Earthquake Monitoring System',
                'system_email' => 'noreply@eqmonitor.com',
                'alert_email' => 'alerts@eqmonitor.com',
                'alert_sms' => null,
                'data_retention_days' => 90,
                'auto_cleanup' => true,
                'enable_email_alerts' => true,
                'enable_sms_alerts' => false,
                'maintenance_mode' => false,
                'timezone' => 'Asia/Jakarta',
                'language' => 'en',
                'map_provider' => 'openstreetmap',
                'map_api_key' => null,
                'refresh_interval' => 30,
                'max_log_size' => 100,
                'backup_enabled' => true,
                'backup_frequency' => 'daily',

                // Notification defaults
                'notify_warning' => true,
                'notify_danger' => true,
                'notify_device_offline' => true,
                'notify_device_online' => false,
                'email_template_warning' => 'Warning: Earthquake detected with magnitude {magnitude} at {location}',
                'email_template_danger' => 'DANGER: Major earthquake detected with magnitude {magnitude} at {location}. Immediate action required!',
                'sms_template_warning' => 'EQ Warning: Mag {magnitude} at {location}',
                'sms_template_danger' => 'EQ DANGER: Mag {magnitude} at {location}. Take cover!',
                'push_notifications' => true,
                'slack_webhook' => null,
                'telegram_bot_token' => null,
                'telegram_chat_id' => null,

                // Security defaults
                'require_2fa' => false,
                'session_timeout' => 60,
                'max_login_attempts' => 5,
                'password_expiry_days' => 90,
                'ip_whitelist' => null,
                'api_rate_limit' => 100,
                'enable_audit_log' => true,
                'data_encryption' => true,
            ];

            return array_merge($defaults, $settings);
        });
    }

    /**
     * Export settings to JSON file.
     */
    public function export()
    {
        $settings = $this->getSystemSettings();
        $thresholds = Threshold::all()->toArray();

        $data = [
            'settings' => $settings,
            'thresholds' => $thresholds,
            'exported_at' => now()->toISOString(),
            'system_version' => '1.0.0'
        ];

        $filename = 'system-settings-' . date('Y-m-d-H-i-s') . '.json';

        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Import settings from JSON file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'settings_file' => ['required', 'file', 'mimes:json', 'max:2048']
        ]);

        try {
            $content = file_get_contents($request->file('settings_file')->getRealPath());
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON file');
            }

            // Import thresholds
            if (isset($data['thresholds']) && is_array($data['thresholds'])) {
                foreach ($data['thresholds'] as $threshold) {
                    if (isset($threshold['id'])) {
                        Threshold::updateOrCreate(
                            ['id' => $threshold['id']],
                            [
                                'min_value' => $threshold['min_value'],
                                'description' => $threshold['description']
                            ]
                        );
                    }
                }
            }

            // Import settings
            if (isset($data['settings']) && is_array($data['settings'])) {
                foreach ($data['settings'] as $key => $value) {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value]
                    );
                }
            }

            // Clear all caches
            Cache::flush();

            return redirect()->route('settings.index')
                ->with('success', 'Settings imported successfully.');

        } catch (\Exception $e) {
            return redirect()->route('settings.index')
                ->with('error', 'Failed to import settings: ' . $e->getMessage());
        }
    }

    /**
     * Reset settings to defaults.
     */
    public function reset()
    {
        // Delete all custom settings
        Setting::whereNotIn('key', [])->delete();

        // Reset thresholds to default
        Threshold::truncate();
        Threshold::insert([
            ['min_value' => 3.0, 'description' => 'warning', 'created_at' => now(), 'updated_at' => now()],
            ['min_value' => 5.0, 'description' => 'danger', 'created_at' => now(), 'updated_at' => now()]
        ]);

        // Clear all caches
        Cache::flush();

        return redirect()->route('settings.index')
            ->with('success', 'All settings have been reset to default values.');
    }

    /**
     * Clear system cache.
     */
    public function clearCache()
    {
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'System cache cleared successfully.'
        ]);
    }

    /**
     * Get system information.
     */
    public function systemInfo()
    {
        $info = [
            'system' => [
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'database' => config('database.default'),
                'timezone' => config('app.timezone'),
                'environment' => config('app.env'),
                'debug_mode' => config('app.debug'),
            ],
            'resources' => [
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
            ],
            'storage' => [
                'total' => disk_total_space('/'),
                'free' => disk_free_space('/'),
                'used' => disk_total_space('/') - disk_free_space('/'),
            ],
            'application' => [
                'users_count' => \App\Models\User::count(),
                'devices_count' => \App\Models\Device::count(),
                'events_count' => \App\Models\EarthquakeEvent::count(),
                'logs_count' => \App\Models\DeviceLog::count(),
            ]
        ];

        // Format storage sizes
        foreach ($info['storage'] as $key => $value) {
            $info['storage'][$key . '_formatted'] = $this->formatBytes($value);
        }

        return response()->json([
            'success' => true,
            'system_info' => $info
        ]);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
