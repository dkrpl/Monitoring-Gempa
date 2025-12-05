<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->nullable()->after('city');
            $table->string('timezone')->default('Asia/Jakarta')->after('country');
            $table->boolean('email_notifications')->default(true)->after('timezone');
            $table->boolean('sms_notifications')->default(false)->after('email_notifications');
            $table->boolean('push_notifications')->default(true)->after('sms_notifications');
            $table->enum('language', ['en', 'id'])->default('en')->after('push_notifications');
            $table->text('bio')->nullable()->after('language');
            $table->timestamp('email_verified_at')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'address', 'city', 'country', 'timezone',
                'email_notifications', 'sms_notifications', 'push_notifications',
                'language', 'bio', 'email_verified_at'
            ]);
        });
    }
};
