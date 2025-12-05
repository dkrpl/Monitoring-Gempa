<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thresholds', function (Blueprint $table) {
            $table->id();
            $table->float('min_value');
            $table->string('description');
            $table->timestamps();
        });

        // Insert default thresholds
        DB::table('thresholds')->insert([
            ['min_value' => 3.0, 'description' => 'warning', 'created_at' => now(), 'updated_at' => now()],
            ['min_value' => 5.0, 'description' => 'Danger', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('thresholds');
    }
};
