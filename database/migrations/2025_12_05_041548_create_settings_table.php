<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'system_name', 'value' => 'EQMonitor - Earthquake Monitoring System', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'system_email', 'value' => 'noreply@eqmonitor.com', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'alert_email', 'value' => 'alerts@eqmonitor.com', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'data_retention_days', 'value' => '90', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'auto_cleanup', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'enable_email_alerts', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'enable_sms_alerts', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'timezone', 'value' => 'Asia/Jakarta', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'language', 'value' => 'en', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'map_provider', 'value' => 'openstreetmap', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'refresh_interval', 'value' => '30', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_log_size', 'value' => '100', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'backup_enabled', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'backup_frequency', 'value' => 'daily', 'created_at' => now(), 'updated_at' => now()],

            // Notification settings
            ['key' => 'notify_warning', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'notify_danger', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'notify_device_offline', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'notify_device_online', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'push_notifications', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],

            // Security settings
            ['key' => 'session_timeout', 'value' => '60', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_login_attempts', 'value' => '5', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'password_expiry_days', 'value' => '90', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'api_rate_limit', 'value' => '100', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'enable_audit_log', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'data_encryption', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
