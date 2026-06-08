<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RidersTableSeeder extends Seeder
{
    public function run()
    {
        $riders = [
            [
                'name' => 'John Doe',
                'email' => 'john.rider@example.com',
                'password' => Hash::make('password123'),
                'role' => 'rider',
                'is_active' => true,
                'phone' => '+1234567890',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.rider@example.com',
                'password' => Hash::make('password123'),
                'role' => 'rider',
                'is_active' => true,
                'phone' => '+1234567891',
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.rider@example.com',
                'password' => Hash::make('password123'),
                'role' => 'rider',
                'is_active' => true,
                'phone' => '+1234567892',
            ],
        ];
        
        foreach ($riders as $rider) {
            User::create($rider);
        }
        
        $this->command->info('Riders created successfully!');
    }
}