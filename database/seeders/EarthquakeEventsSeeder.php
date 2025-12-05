<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EarthquakeEvent;
use App\Models\Device;
use Carbon\Carbon;

class EarthquakeEventsSeeder extends Seeder
{
    public function run(): void
    {
        $devices = Device::all();

        if ($devices->isEmpty()) {
            $this->command->info('No devices found. Please run DevicesSeeder first.');
            return;
        }

        foreach ($devices as $device) {
            // Create earthquake events for the last 90 days
            for ($i = 0; $i < 90; $i++) {
                $date = now()->subDays($i);

                // Randomly create events (1 in 3 chance per day for active devices)
                if ($device->status === 'aktif' && rand(1, 3) === 1) {
                    $eventTime = $date->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59));
                    $magnitude = rand(10, 60) / 10; // 1.0 to 6.0
                    $status = $magnitude >= 5.0 ? 'danger' : ($magnitude >= 3.0 ? 'warning' : 'normal');

                    // Only create warning and danger events
                    if ($status !== 'normal') {
                        EarthquakeEvent::create([
                            'device_id' => $device->id,
                            'magnitude' => $magnitude,
                            'status' => $status,
                            'occurred_at' => $eventTime,
                            'latitude' => rand(-90000000, 90000000) / 1000000,
                            'longitude' => rand(-180000000, 180000000) / 1000000,
                            'depth' => rand(1, 1000) / 10, // 0.1 to 100 km
                            'description' => $this->getRandomDescription($magnitude, $status)
                        ]);
                    }
                }
            }
        }

        $this->command->info('Earthquake events seeded successfully!');
    }

    private function getRandomDescription($magnitude, $status)
    {
        $descriptions = [
            'warning' => [
                'Light tremor detected. No damage reported.',
                'Minor earthquake felt in the area.',
                'Small seismic activity recorded.',
                'Earthquake detected, magnitude below danger threshold.',
                'Vibration sensors triggered warning alert.'
            ],
            'danger' => [
                'Strong earthquake detected! Potential damage possible.',
                'Major seismic event recorded. Alert level raised.',
                'Dangerous earthquake magnitude detected.',
                'Significant ground movement recorded.',
                'Earthquake above safety threshold! Immediate action recommended.'
            ]
        ];

        $list = $descriptions[$status] ?? ['Earthquake event recorded.'];
        return $list[array_rand($list)];
    }
}
