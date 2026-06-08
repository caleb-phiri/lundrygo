<?php
// app/Http/Requests/UpdateStudentRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $studentId = $this->route('student');
        
        return [
            'first_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'email' => [
                'required',
                'email',
                Rule::unique('students')->ignore($studentId),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|min:10|max:500',
            'date_of_birth' => 'required|date|before:today',
            'course' => 'required|string|max:100',
            'year_level' => 'required|integer|min:1|max:6',
            'semester' => 'required|in:1st,2nd',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'status' => 'required|in:active,inactive,graduated,suspended',
            'notes' => 'nullable|string|max:1000',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}