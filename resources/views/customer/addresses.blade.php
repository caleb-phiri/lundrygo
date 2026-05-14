@extends('layouts.app')

@section('title', 'My Addresses')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action">My Orders</a>
                <a href="{{ route('customer.orders.create') }}" class="list-group-item list-group-item-action">New Order</a>
                <a href="{{ route('customer.addresses') }}" class="list-group-item list-group-item-action active">Addresses</a>
                <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action">Profile</a>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Addresses</h4>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">+ Add New Address</button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(isset($addresses) && $addresses->count() > 0)
                        <div class="row">
                            @foreach($addresses as $address)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 {{ $address->is_default ? 'border-primary' : '' }}">
                                    <div class="card-body">
                                        <h6>{{ $address->label }} @if($address->is_default)<span class="badge bg-primary">Default</span>@endif</h6>
                                        <p class="text-muted small mb-2">{{ $address->full_address }}</p>
                                        <p class="text-muted small mb-2">Phone: {{ $address->contact_phone ?? 'N/A' }}</p>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary edit-address" data-id="{{ $address->id }}" data-label="{{ $address->label }}" data-address="{{ $address->full_address }}" data-phone="{{ $address->contact_phone }}">Edit</button>
                                            @if(!$address->is_default)
                                                <form method="POST" action="{{ route('customer.addresses.default', $address) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success">Set as Default</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('customer.addresses.delete', $address) }}" class="d-inline" onsubmit="return confirm('Delete this address?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No addresses saved yet.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">Add Your First Address</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('customer.addresses.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Label</label>
                        <select name="label" class="form-select" required>
                            <option value="Home">Home</option>
                            <option value="Work">Work</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Address</label>
                        <textarea name="full_address" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_default" class="form-check-input" value="1">
                        <label class="form-check-label">Set as default address</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection