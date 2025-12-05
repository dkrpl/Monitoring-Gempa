<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan tabel settings sudah ada
        if (!Schema::hasTable('settings')) {
            $this->command->error('Settings table does not exist. Please run migrations first.');
            return;
        }

        // Hapus semua data settings yang ada
        Setting::truncate();

        // Definisikan semua default settings
        $defaultSettings = [
            // General Settings
            [
                'key' => 'system_name',
                'value' => 'EQMonitor - Earthquake Monitoring System',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'system_email',
                'value' => 'noreply@eqmonitor.com',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'alert_email',
                'value' => 'alerts@eqmonitor.com',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'alert_sms',
                'value' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Data Management
            [
                'key' => 'data_retention_days',
                'value' => '90',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'auto_cleanup',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'max_log_size',
                'value' => '100',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'backup_enabled',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Alert Settings
            [
                'key' => 'enable_email_alerts',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'enable_sms_alerts',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // System Configuration
            [
                'key' => 'timezone',
                'value' => 'Asia/Jakarta',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'language',
                'value' => 'en',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'map_provider',
                'value' => 'openstreetmap',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'map_api_key',
                'value' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'refresh_interval',
                'value' => '30',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Notification Settings
            [
                'key' => 'notify_warning',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'notify_danger',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'notify_device_offline',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'notify_device_online',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'push_notifications',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Email Templates
            [
                'key' => 'email_template_warning',
                'value' => 'Warning: Earthquake detected with magnitude {magnitude} at {location}',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'email_template_danger',
                'value' => 'DANGER: Major earthquake detected with magnitude {magnitude} at {location}. Immediate action required!',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // SMS Templates
            [
                'key' => 'sms_template_warning',
                'value' => 'EQ Warning: Mag {magnitude} at {location}',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'sms_template_danger',
                'value' => 'EQ DANGER: Mag {magnitude} at {location}. Take cover!',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Third-Party Integrations
            [
                'key' => 'slack_webhook',
                'value' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'telegram_bot_token',
                'value' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'telegram_chat_id',
                'value' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Security Settings
            [
                'key' => 'require_2fa',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'session_timeout',
                'value' => '60',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'password_expiry_days',
                'value' => '90',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'ip_whitelist',
                'value' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'api_rate_limit',
                'value' => '100',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'enable_audit_log',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'data_encryption',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Additional Settings
            [
                'key' => 'default_latitude',
                'value' => '-6.2088',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'default_longitude',
                'value' => '106.8456',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'max_earthquake_magnitude',
                'value' => '10.0',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'min_earthquake_magnitude',
                'value' => '0.0',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'alert_sound_enabled',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'alert_sound_file',
                'value' => 'alert.mp3',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'auto_refresh_dashboard',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'dashboard_refresh_interval',
                'value' => '10',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        // Insert settings ke database
        foreach ($defaultSettings as $setting) {
            Setting::create([
                'key' => $setting['key'],
                'value' => $setting['value']
            ]);
        }

        $this->command->info('Settings seeded successfully!');
        $this->command->info('Total settings created: ' . count($defaultSettings));
    }
}
