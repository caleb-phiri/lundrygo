<?php
// database/seeders/StudentSeeder.php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $courses = ['Computer Science', 'Information Technology', 'Business Administration', 'Engineering'];
        $statuses = ['active', 'inactive', 'graduated', 'suspended'];
        
        for ($i = 0; $i < 100; $i++) {
            Student::create([
                'student_id' => 'STU-' . date('Y') . '-' . strtoupper($faker->unique()->bothify('???###')),
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'date_of_birth' => $faker->dateTimeBetween('-25 years', '-18 years'),
                'course' => $faker->randomElement($courses),
                'year_level' => $faker->numberBetween(1, 6),
                'semester' => $faker->randomElement(['1st', '2nd']),
                'gpa' => $faker->randomFloat(2, 1.0, 4.0),
                'status' => $faker->randomElement($statuses),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}