<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Traits\LogsActivity;

class AdminSeeder extends Seeder
{
    use LogsActivity;

    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@eqmonitor.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '+62123456789',
            'address' => 'Jl. Sudirman No. 123',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'timezone' => 'Asia/Jakarta',
            'email_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => true,
            'language' => 'en',
            'bio' => 'System Administrator',
            'email_verified_at' => now()
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@eqmonitor.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'phone' => '+628987654321',
            'address' => 'Jl. Thamrin No. 456',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'timezone' => 'Asia/Jakarta',
            'email_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => true,
            'language' => 'id',
            'bio' => 'Regular user for testing',
            'email_verified_at' => now()
        ]);

        // Log the creation
        $this->logActivity('user_created', 'Admin user created', $admin);
        $this->logActivity('user_created', 'Test user created', $user);
    }
}
