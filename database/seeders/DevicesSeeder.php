<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use Illuminate\Support\Str;

class DevicesSeeder extends Seeder
{
    public function run(): void
    {
        $devices = [
            [
                'nama_device' => 'SW-420 Sensor #001',
                'lokasi' => 'Building A, 3rd Floor',
                'status' => 'aktif',
                'last_seen' => now()
            ],
            [
                'nama_device' => 'SW-420 Sensor #002',
                'lokasi' => 'Building B, Ground Floor',
                'status' => 'aktif',
                'last_seen' => now()->subMinutes(30)
            ],
            [
                'nama_device' => 'SW-420 Sensor #003',
                'lokasi' => 'Data Center, Server Room',
                'status' => 'aktif',
                'last_seen' => now()->subHours(2)
            ],
            [
                'nama_device' => 'SW-420 Sensor #004',
                'lokasi' => 'Laboratory Building',
                'status' => 'nonaktif',
                'last_seen' => null
            ],
            [
                'nama_device' => 'SW-420 Sensor #005',
                'lokasi' => 'Administration Building',
                'status' => 'aktif',
                'last_seen' => now()->subMinutes(15)
            ],
        ];

        foreach ($devices as $deviceData) {
            Device::create([
                'uuid' => Str::uuid(),
                'nama_device' => $deviceData['nama_device'],
                'lokasi' => $deviceData['lokasi'],
                'status' => $deviceData['status'],
                'last_seen' => $deviceData['last_seen']
            ]);
        }

        $this->command->info('Devices seeded successfully!');
    }
}
