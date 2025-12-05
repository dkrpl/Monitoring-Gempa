<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Threshold;

class ThresholdsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing thresholds
        Threshold::truncate();

        // Create default thresholds
        $thresholds = [
            [
                'min_value' => 3.0,
                'description' => 'warning',
                'color' => '#ffc107',
                'notification_enabled' => true,
                'notification_message' => 'Warning: Earthquake magnitude {magnitude} detected at {location}. Be prepared.',
                'auto_alert' => false,
                'priority' => 1
            ],
            [
                'min_value' => 5.0,
                'description' => 'danger',
                'color' => '#e74a3b',
                'notification_enabled' => true,
                'notification_message' => 'DANGER: Strong earthquake magnitude {magnitude} detected at {location}! Take safety precautions immediately.',
                'auto_alert' => true,
                'priority' => 2
            ],
            [
                'min_value' => 7.0,
                'description' => 'critical',
                'color' => '#dc3545',
                'notification_enabled' => true,
                'notification_message' => 'CRITICAL: Major earthquake magnitude {magnitude} detected! EVACUATE if necessary. Location: {location}',
                'auto_alert' => true,
                'priority' => 3
            ]
        ];

        foreach ($thresholds as $threshold) {
            Threshold::create($threshold);
        }

        $this->command->info('Default thresholds seeded successfully!');
    }
}
