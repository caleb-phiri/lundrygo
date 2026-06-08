<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\LaundryService;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@laundry.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
        
        // Create Rider
        User::create([
            'name' => 'John Rider',
            'email' => 'rider@laundry.com',
            'password' => Hash::make('password'),
            'role' => 'rider',
            'is_active' => true,
        ]);
        
        // Create Customer
        User::create([
            'name' => 'Jane Customer',
            'email' => 'customer@laundry.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_active' => true,
        ]);
        
        // Create Categories
        $categories = [
            ['name' => 'Wash & Fold', 'slug' => 'wash-fold', 'is_active' => true],
            ['name' => 'Dry Cleaning', 'slug' => 'dry-cleaning', 'is_active' => true],
            ['name' => 'Ironing Only', 'slug' => 'ironing-only', 'is_active' => true],
            ['name' => 'Linen Service', 'slug' => 'linen-service', 'is_active' => true],
        ];
        
        foreach ($categories as $category) {
            Category::create($category);
        }
        
        // Create Services
        $services = [
            ['name' => 'Standard Wash & Fold', 'category_id' => 1, 'price' => 6.00, 'unit' => 'kg', 'is_active' => true, 'estimated_hours' => 24],
            ['name' => 'Express Wash & Fold', 'category_id' => 1, 'price' => 10.00, 'unit' => 'kg', 'is_active' => true, 'estimated_hours' => 12],
            ['name' => 'Suit Dry Cleaning', 'category_id' => 2, 'price' => 15.00, 'unit' => 'piece', 'is_active' => true, 'estimated_hours' => 48],
            ['name' => 'Shirt Ironing', 'category_id' => 3, 'price' => 2.50, 'unit' => 'piece', 'is_active' => true, 'estimated_hours' => 24],
            ['name' => 'Bedsheet Cleaning', 'category_id' => 4, 'price' => 12.00, 'unit' => 'piece', 'is_active' => true, 'estimated_hours' => 48],
            ['name' => 'Duvet Cleaning', 'category_id' => 4, 'price' => 25.00, 'unit' => 'piece', 'is_active' => true, 'estimated_hours' => 48],
        ];
        
        foreach ($services as $service) {
            LaundryService::create($service);
        }
        
        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('Admin:    admin@laundry.com / password');
        $this->command->info('Rider:    rider@laundry.com / password');
        $this->command->info('Customer: customer@laundry.com / password');
    }
}