@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-2">
            @include('admin.partials.sidebar')
        </div>
        
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Users Management</h5>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New User
                    </a>
                </div>
                <div class="card-body">
                    <!-- Stats Row -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="alert alert-primary text-center">
                                <strong>Total Users:</strong><br>
                                {{ $stats['total'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="alert alert-info text-center">
                                <strong>Customers:</strong><br>
                                {{ $stats['customers'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="alert alert-success text-center">
                                <strong>Riders:</strong><br>
                                {{ $stats['riders'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="alert alert-warning text-center">
                                <strong>Admins:</strong><br>
                                {{ $stats['admins'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="alert alert-secondary text-center">
                                <strong>Supervisors:</strong><br>
                                {{ $stats['supervisors'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="alert alert-success text-center">
                                <strong>Active:</strong><br>
                                {{ $stats['active'] ?? 0 }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search and Filter -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by name or email..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="role" class="form-select">
                                    <option value="">All Roles</option>
                                    <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                                    <option value="rider" {{ request('role') == 'rider' ? 'selected' : '' }}>Rider</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary w-100">
                                    <i class="fas fa-sync-alt"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        @if($user->role == 'rider')
                                            <i class="fas fa-motorcycle text-success"></i>
                                        @elseif($user->role == 'admin')
                                            <i class="fas fa-shield-alt text-danger"></i>
                                        @else
                                            <i class="fas fa-user text-primary"></i>
                                        @endif
                                        {{ $user->name }}
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $user->role == 'admin' ? 'danger' : 
                                            ($user->role == 'rider' ? 'success' : 
                                            ($user->role == 'supervisor' ? 'warning' : 'info'))
                                        }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(auth()->id() != $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                        No users found.
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary ms-2">
                                            Create First User
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection