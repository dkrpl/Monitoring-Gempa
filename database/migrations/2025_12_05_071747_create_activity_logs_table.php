<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('action'); // e.g., 'login', 'logout', 'create_device', 'update_profile'
            $table->string('description');
            $table->json('details')->nullable(); // Additional data in JSON format
            $table->string('model_type')->nullable(); // e.g., 'App\Models\User'
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the related model
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index('model_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
