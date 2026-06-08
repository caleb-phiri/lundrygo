<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );
        
        // Create Regular Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
        
        // Create Manager
        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password123'),
                'role' => 'manager',
                'is_active' => true,
            ]
        );
        
        $this->command->info('Admin users created successfully!');
        $this->command->info('Super Admin: superadmin@example.com / password123');
        $this->command->info('Admin: admin@example.com / password123');
        $this->command->info('Manager: manager@example.com / password123');
    }
}