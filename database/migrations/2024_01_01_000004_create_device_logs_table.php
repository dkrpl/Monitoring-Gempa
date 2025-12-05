<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->float('magnitude')->nullable();
            $table->dateTime('logged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_logs');
    }
};
