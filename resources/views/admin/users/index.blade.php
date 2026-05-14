@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2">
            @include('admin.partials.sidebar')
        </div>
        
        <!-- Main Content -->
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
                        <div class="col-md-3">
                            <div class="alert alert-info">
                                <strong>Total Users:</strong> {{ $stats['total'] }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-success">
                                <strong>Customers:</strong> {{ $stats['customers'] }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-warning">
                                <strong>Riders:</strong> {{ $stats['riders'] }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-primary">
                                <strong>Active Users:</strong> {{ $stats['active'] }}
                            </div>
                        </div>
                    </div>
                    
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
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $user->role == 'admin' ? 'danger' : 
                                            ($user->role == 'super_admin' ? 'dark' :
                                            ($user->role == 'rider' ? 'info' : 'secondary'))
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
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                data-user-id="{{ $user->id }}"
                                                onclick="deleteUser(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No users found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteUser(button) {
    const userId = button.getAttribute('data-user-id');
    if (confirm('Are you sure you want to delete this user?')) {
        const form = document.getElementById('delete-form');
        // Use url() helper instead of route() to avoid parameter validation
        form.action = "{{ url('admin/users') }}/" + userId;
        form.submit();
    }
}
</script>
@endsection