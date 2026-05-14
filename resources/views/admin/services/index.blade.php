@extends('layouts.app')

@section('title', 'Manage Services')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-2">
            @include('admin.partials.sidebar')
        </div>
        
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Services Management</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addServiceModal" @if($categories->isEmpty()) disabled @endif>
                        <i class="fas fa-plus"></i> Add Service
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($categories->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>No categories found!</strong> 
                            Please <a href="{{ route('admin.categories.index') }}" class="alert-link">create a category</a> first before adding services.
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                <tr>
                                    <td>{{ $service->id }}</td>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ $service->category->name ?? 'Uncategorized' }}</td>
                                    <td>${{ number_format($service->price, 2) }}</td>
                                    <td>{{ ucfirst($service->unit ?? 'Item') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $service->is_active ? 'success' : 'danger' }}">
                                            {{ $service->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary edit-service" 
                                                data-id="{{ $service->id }}"
                                                data-name="{{ $service->name }}"
                                                data-price="{{ $service->price }}"
                                                data-category="{{ $service->category_id }}"
                                                data-unit="{{ $service->unit }}"
                                                data-description="{{ $service->description }}"
                                                data-active="{{ $service->is_active ? '1' : '0' }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-service" 
                                                data-id="{{ $service->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        No services found. 
                                        @if($categories->isEmpty())
                                            <a href="{{ route('admin.categories.index') }}">Create a category first</a>
                                        @else
                                            Click "Add Service" to create one.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $services->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.services.store') }}" id="addServiceForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Service Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($categories->isEmpty())
                            <div class="text-danger mt-1">
                                <strong>No categories available!</strong> 
                                <a href="{{ route('admin.categories.index') }}">Click here to create a category first</a>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select" id="unit" name="unit" required>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="piece">Piece</option>
                            <option value="item">Item</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveServiceBtn">Save Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editServiceForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Service Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" name="category_id" id="edit_category_id" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select" name="unit" id="edit_unit" required>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="piece">Piece</option>
                            <option value="item">Item</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" id="edit_is_active" value="1">
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Edit service handler
document.querySelectorAll('.edit-service').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('edit_name').value = this.dataset.name;
        document.getElementById('edit_price').value = this.dataset.price;
        document.getElementById('edit_category_id').value = this.dataset.category;
        document.getElementById('edit_unit').value = this.dataset.unit || 'item';
        document.getElementById('edit_description').value = this.dataset.description || '';
        document.getElementById('edit_is_active').checked = this.dataset.active === '1';
        document.getElementById('editServiceForm').action = `/admin/services/${this.dataset.id}`;
        
        new bootstrap.Modal(document.getElementById('editServiceModal')).show();
    });
});

// Delete service handler
document.querySelectorAll('.delete-service').forEach(button => {
    button.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/services/${this.dataset.id}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });
});

// Validate form before submission
document.getElementById('addServiceForm')?.addEventListener('submit', function(e) {
    const categorySelect = document.getElementById('category_id');
    const selectedCategory = categorySelect.value;
    
    if (!selectedCategory) {
        e.preventDefault();
        alert('Please select a category before saving the service.');
        return false;
    }
    
    // You can add more validation here
    console.log('Submitting service with category_id:', selectedCategory);
});
</script>
@endsection