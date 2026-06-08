<?php
// app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Student extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $table = 'students';
    
    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'course',
        'year_level',
        'semester',
        'gpa',
        'status',
        'profile_photo',
        'notes',
        'email_verified_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'email_verified_at' => 'datetime',
        'gpa' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['full_name', 'age', 'status_badge_color'];

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'graduated' => 'info',
            'suspended' => 'danger',
            default => 'secondary',
        };
    }

    public function getAcademicStandingAttribute(): string
    {
        if (!$this->gpa) return 'Not Available';
        
        return match(true) {
            $this->gpa >= 3.5 => 'Excellent',
            $this->gpa >= 3.0 => 'Good',
            $this->gpa >= 2.0 => 'Satisfactory',
            default => 'Academic Probation',
        };
    }

    // Mutators
    public function setFirstNameAttribute($value): void
    {
        $this->attributes['first_name'] = ucfirst(strtolower($value));
    }

    public function setLastNameAttribute($value): void
    {
        $this->attributes['last_name'] = ucfirst(strtolower($value));
    }

    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = strtolower($value);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCourse($query, $course)
    {
        return $query->where('course', $course);
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('first_name', 'like', "%{$searchTerm}%")
              ->orWhere('last_name', 'like', "%{$searchTerm}%")
              ->orWhere('student_id', 'like', "%{$searchTerm}%")
              ->orWhere('email', 'like', "%{$searchTerm}%");
        });
    }

    // Relationships
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}