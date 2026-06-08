{{-- resources/views/students/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Student Management System')

@section('header')
    <div class="bg-gradient-primary pb-5 pt-4">
        <div class="container">
            <h1 class="text-white mb-0">
                <i class="fas fa-users me-2"></i>Student Management
            </h1>
            <p class="text-white-50 mb-0">Manage student records, track academic progress, and generate reports</p>
        </div>
    </div>
@endsection

@section('content')
<div class="container mt--4">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Total Students</h5>
                            <span class="h2 font-weight-bold mb-0">{{ number_format($stats['total']) }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-primary text-white rounded-circle shadow">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Active Students</h5>
                            <span class="h2 font-weight-bold mb-0">{{ number_format($stats['active']) }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Graduated</h5>
                            <span class="h2 font-weight-bold mb-0">{{ number_format($stats['graduated']) }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Average GPA</h5>
                            <span class="h2 font-weight-bold mb-0">{{ number_format($stats['avg_gpa'] ?? 0, 2) }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card shadow">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Student Records</h3>
                </div>
                <div class="col text-right">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addStudentModal">
                        <i class="fas fa-plus"></i> Add New Student
                    </button>
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#importModal">
                        <i class="fas fa-upload"></i> Import
                    </button>
                    <a href="{{ route('students.export') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-download"></i> Export
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Search and Filter -->
            <form method="GET" action="{{ route('students.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search by name, ID, or email..." 
                                   value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="course" class="form-control">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course }}" {{ request('course') == $course ? 'selected' : '' }}>
                                    {{ $course }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="year_level" class="form-control">
                            <option value="">All Years</option>
                            @for($i=1; $i<=6; $i++)
                                <option value="{{ $i }}" {{ request('year_level') == $i ? 'selected' : '' }}>
                                    Year {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">Apply Filters</button>
                    </div>
                </div>
            </form>

            <!-- Students Table -->
            <div class="table-responsive">
                <table class="table table-hover align-items-center">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Student Info</th>
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>GPA</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td>
                                <span class="badge badge-primary">{{ $student->student_id }}</span>
                            </td>
                            <td>
                                <div class="media align-items-center">
                                    <div class="avatar rounded-circle mr-3">
                                        @if($student->profile_photo)
                                            <img src="{{ Storage::url($student->profile_photo) }}" 
                                                 class="rounded-circle" width="40" height="40">
                                        @else
                                            <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="media-body">
                                        <span class="mb-0 text-sm font-weight-bold">{{ $student->full_name }}</span>
                                        <br>
                                        <small class="text-muted">{{ $student->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $student->course }}</td>
                            <td>Year {{ $student->year_level }} - {{ $student->semester }} Sem</td>
                            <td>
                                @if($student->gpa)
                                    <span class="font-weight-bold">{{ number_format($student->gpa, 2) }}</span>
                                    <small class="text-muted">({{ $student->academic_standing }})</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $student->status_badge_color }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('students.show', $student) }}" 
                                       class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('students.edit', $student) }}" 
                                       class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete({{ $student->id }})"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $student->id }}" 
                                          action="{{ route('students.destroy', $student) }}" 
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5>No students found</h5>
                                <p class="text-muted">Start by adding your first student record.</p>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#addStudentModal">
                                    <i class="fas fa-plus"></i> Add Student
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Register New Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Address <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" rows="2" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_birth" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Course <span class="text-danger">*</span></label>
                                <select name="course" class="form-control" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course }}">{{ $course }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Year Level</label>
                                <select name="year_level" class="form-control">
                                    @for($i=1; $i<=6; $i++)
                                        <option value="{{ $i }}">Year {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Semester</label>
                                <select name="semester" class="form-control">
                                    <option value="1st">1st Semester</option>
                                    <option value="2nd">2nd Semester</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Profile Photo</label>
                                <input type="file" name="profile_photo" class="form-control-file" accept="image/*">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Additional Notes</label>
                        <textarea name="notes" class="form-control" rows="2" 
                                  placeholder="Any additional information about the student..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('students.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Import Students from CSV</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        CSV file must have headers: first_name, last_name, email, course, year_level
                    </div>
                    <div class="form-group">
                        <label>Choose CSV File</label>
                        <input type="file" name="csv_file" class="form-control-file" accept=".csv" required>
                    </div>
                    <a href="{{ route('students.export') }}?format=csv_template" class="btn btn-sm btn-link">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import Students</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(87deg, #5e72e4 0%, #825ee4 100%);
    }
    .card-stats .card-body {
        padding: 1rem 1.5rem;
    }
    .icon-shape {
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-placeholder {
        font-weight: bold;
        font-size: 18px;
    }
    .table th, .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
function confirmDelete(studentId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + studentId).submit();
        }
    })
}

$(document).ready(function() {
    @if(session('success'))
        Swal.fire('Success!', '{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        Swal.fire('Error!', '{{ session('error') }}', 'error');
    @endif
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush