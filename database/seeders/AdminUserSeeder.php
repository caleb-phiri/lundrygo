<?php
// Run: php artisan make:seeder AdminUserSeeder

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'employee_number' => 'ADMIN001',
            'name' => 'Super Admin',
            'email' => 'admin@laundrypro.com',
            'password' => Hash::make('password123'),
            'user_type' => 'admin',
            'is_active' => true,
            'onboarded' => true,
            'employment_status' => 'active'
        ]);
    }
}

// Then run: php artisan db:seed --class=AdminUserSeeder