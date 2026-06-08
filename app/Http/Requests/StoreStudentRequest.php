<?php
// app/Http/Requests/StoreStudentRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'registrar']);
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:255|unique:students,email',
            'phone' => 'nullable|string|max:20|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/',
            'address' => 'required|string|min:10|max:500',
            'date_of_birth' => 'required|date|before:today|after:1900-01-01',
            'course' => 'required|string|max:100',
            'year_level' => 'required|integer|min:1|max:6',
            'semester' => 'required|in:1st,2nd',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'notes' => 'nullable|string|max:1000',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'first_name.regex' => 'First name can only contain letters and spaces',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email address is required',
            'email.unique' => 'This email is already registered',
            'email.email' => 'Please enter a valid email address',
            'phone.regex' => 'Please enter a valid phone number',
            'address.min' => 'Please enter your complete address',
            'date_of_birth.before' => 'Date of birth must be in the past',
            'date_of_birth.after' => 'Invalid date of birth',
            'gpa.min' => 'GPA cannot be negative',
            'gpa.max' => 'GPA cannot exceed 4.0',
            'profile_photo.image' => 'Profile photo must be an image',
            'profile_photo.max' => 'Profile photo cannot exceed 2MB',
        ];
    }
}