<?php
// database/migrations/2024_01_01_000001_create_students_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address');
            $table->date('date_of_birth');
            $table->string('course');
            $table->integer('year_level')->default(1);
            $table->enum('semester', ['1st', '2nd'])->default('1st');
            $table->decimal('gpa', 3, 2)->nullable();
            $table->enum('status', ['active', 'inactive', 'graduated', 'suspended'])->default('active');
            $table->string('profile_photo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index(['student_id', 'email']);
            $table->index('course');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};