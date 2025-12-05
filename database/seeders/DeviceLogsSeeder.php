<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\EarthquakeEvent;
use Carbon\Carbon;

class DeviceLogsSeeder extends Seeder
{
    public function run(): void
    {
        $devices = Device::all();

        if ($devices->isEmpty()) {
            $this->command->info('No devices found. Please run DevicesSeeder first.');
            return;
        }

        foreach ($devices as $device) {
            // Create logs for the last 30 days
            for ($i = 0; $i < 30; $i++) {
                $date = now()->subDays($i);

                // Create 1-5 logs per day
                $logsCount = rand(1, 5);
                for ($j = 0; $j < $logsCount; $j++) {
                    $logTime = $date->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59));

                    DeviceLog::create([
                        'device_id' => $device->id,
                        'status' => 'online',
                        'magnitude' => rand(0, 10) / 10, // 0.0 to 1.0
                        'logged_at' => $logTime
                    ]);
                }

                // Randomly create earthquake events (1 in 5 chance per day)
                if (rand(1, 5) === 1) {
                    $eventTime = $date->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59));
                    $magnitude = rand(20, 80) / 10; // 2.0 to 8.0
                    $status = $magnitude >= 5.0 ? 'danger' : 'warning';

                    EarthquakeEvent::create([
                        'device_id' => $device->id,
                        'magnitude' => $magnitude,
                        'status' => $status,
                        'occurred_at' => $eventTime
                    ]);
                }
            }

            // Update device last_seen to most recent log
            $latestLog = $device->logs()->latest('logged_at')->first();
            if ($latestLog) {
                $device->update(['last_seen' => $latestLog->logged_at]);
            }
        }

        $this->command->info('Device logs and earthquake events seeded successfully!');
    }
}
