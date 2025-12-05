<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('earthquake_events', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('occurred_at');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->decimal('depth', 8, 2)->nullable()->after('longitude'); // in kilometers
            $table->text('description')->nullable()->after('depth');
        });
    }

    public function down(): void
    {
        Schema::table('earthquake_events', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'depth', 'description']);
        });
    }
};
