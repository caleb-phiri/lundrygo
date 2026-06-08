<?php
// app/Http/Controllers/StudentController.php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_students')->only(['index', 'show']);
        $this->middleware('permission:create_students')->only(['create', 'store']);
        $this->middleware('permission:edit_students')->only(['edit', 'update']);
        $this->middleware('permission:delete_students')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Student::query();
        
        // Search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // Course filter
        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Year level filter
        if ($request->filled('year_level')) {
            $query->where('year_level', $request->year_level);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $students = $query->paginate(15)->withQueryString();
        
        // Statistics
        $stats = [
            'total' => Student::count(),
            'active' => Student::where('status', 'active')->count(),
            'graduated' => Student::where('status', 'graduated')->count(),
            'avg_gpa' => Student::whereNotNull('gpa')->avg('gpa'),
        ];
        
        // Get unique courses for filter
        $courses = Student::distinct()->pluck('course');
        
        return view('students.index', compact('students', 'stats', 'courses'));
    }

    public function create()
    {
        $courses = $this->getAvailableCourses();
        return view('students.create', compact('courses'));
    }

    public function store(StoreStudentRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            
            // Generate unique student ID
            $data['student_id'] = $this->generateStudentId();
            
            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $data['profile_photo'] = $this->uploadProfilePhoto($request->file('profile_photo'));
            }
            
            $student = Student::create($data);
            
            DB::commit();
            
            return redirect()->route('students.show', $student)
                ->with('success', 'Student registered successfully! Student ID: ' . $student->student_id);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to register student: ' . $e->getMessage())
                         ->withInput();
        }
    }

    public function show(Student $student)
    {
        // Load relationships
        $student->load(['enrollments', 'payments']);
        
        // Get academic statistics
        $academicStats = [
            'total_credits' => $student->enrollments()->sum('credits'),
            'current_gpa' => $student->gpa,
            'attendance_rate' => $this->calculateAttendanceRate($student),
        ];
        
        return view('students.show', compact('student', 'academicStats'));
    }

    public function edit(Student $student)
    {
        $courses = $this->getAvailableCourses();
        return view('students.edit', compact('student', 'courses'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            
            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old photo
                if ($student->profile_photo) {
                    Storage::disk('public')->delete($student->profile_photo);
                }
                $data['profile_photo'] = $this->uploadProfilePhoto($request->file('profile_photo'));
            }
            
            $student->update($data);
            
            DB::commit();
            
            return redirect()->route('students.show', $student)
                ->with('success', 'Student information updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update student: ' . $e->getMessage())
                         ->withInput();
        }
    }

    public function destroy(Student $student)
    {
        try {
            // Delete profile photo if exists
            if ($student->profile_photo) {
                Storage::disk('public')->delete($student->profile_photo);
            }
            
            $student->delete();
            
            return redirect()->route('students.index')
                ->with('success', 'Student record deleted successfully!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $students = Student::query();
        
        if ($request->has('course')) {
            $students->where('course', $request->course);
        }
        
        $students = $students->get();
        
        // Export as CSV
        $fileName = 'students_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];
        
        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student ID', 'Full Name', 'Email', 'Course', 'Year Level', 'GPA', 'Status']);
            
            foreach ($students as $student) {
                fputcsv($file, [
                    $student->student_id,
                    $student->full_name,
                    $student->email,
                    $student->course,
                    $student->year_level,
                    $student->gpa,
                    $student->status,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);
        
        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        
        $header = fgetcsv($handle);
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        while (($data = fgetcsv($handle)) !== false) {
            $rowData = array_combine($header, $data);
            
            $validator = validator($rowData, [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:students',
                'course' => 'required|string',
                'year_level' => 'required|integer|min:1|max:6',
            ]);
            
            if ($validator->fails()) {
                $errorCount++;
                $errors[] = "Row {$successCount}: " . $validator->errors()->first();
                continue;
            }
            
            $rowData['student_id'] = $this->generateStudentId();
            Student::create($rowData);
            $successCount++;
        }
        
        fclose($handle);
        
        $message = "$successCount students imported successfully.";
        if ($errorCount > 0) {
            $message .= " $errorCount failed. Errors: " . implode('; ', $errors);
        }
        
        return redirect()->route('students.index')
            ->with('success', $message);
    }

    private function generateStudentId(): string
    {
        $year = date('Y');
        $random = strtoupper(Str::random(6));
        $studentId = "STU-{$year}-{$random}";
        
        // Ensure uniqueness
        while (Student::where('student_id', $studentId)->exists()) {
            $random = strtoupper(Str::random(6));
            $studentId = "STU-{$year}-{$random}";
        }
        
        return $studentId;
    }

    private function uploadProfilePhoto($photo): string
    {
        $fileName = 'student_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
        $path = $photo->storeAs('student_photos', $fileName, 'public');
        return $path;
    }

    private function getAvailableCourses(): array
    {
        return [
            'Computer Science',
            'Information Technology',
            'Business Administration',
            'Accounting',
            'Engineering',
            'Nursing',
            'Education',
            'Psychology',
        ];
    }

    private function calculateAttendanceRate(Student $student): float
    {
        $totalClasses = $student->attendances()->count();
        if ($totalClasses === 0) return 0;
        
        $presentClasses = $student->attendances()->where('status', 'present')->count();
        return round(($presentClasses / $totalClasses) * 100, 2);
    }
}