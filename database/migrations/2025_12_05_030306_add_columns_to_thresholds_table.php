<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thresholds', function (Blueprint $table) {
            $table->string('color', 50)->default('#6c757d')->after('description');
            $table->boolean('notification_enabled')->default(true)->after('color');
            $table->text('notification_message')->nullable()->after('notification_enabled');
            $table->boolean('auto_alert')->default(false)->after('notification_message');
            $table->integer('priority')->default(999)->after('auto_alert');
        });
    }

    public function down(): void
    {
        Schema::table('thresholds', function (Blueprint $table) {
            $table->dropColumn(['color', 'notification_enabled', 'notification_message', 'auto_alert', 'priority']);
        });
    }
};
