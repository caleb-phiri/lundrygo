@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3">
            @include('rider.partials.sidebar')
        </div>
        
        <div class="col-md-9">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2 text-primary"></i> Profile Information</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <form method="POST" action="{{ route('rider.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $rider->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" value="{{ $rider->email }}" disabled>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $rider->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vehicle Number</label>
                                <input type="text" name="vehicle_number" class="form-control" 
                                       value="{{ old('vehicle_number', $rider->vehicle_number ?? '') }}">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Vehicle Type</label>
                            <select name="vehicle_type" class="form-select">
                                <option value="">Select Vehicle Type</option>
                                <option value="motorcycle" {{ ($rider->vehicle_type ?? '') == 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                                <option value="scooter" {{ ($rider->vehicle_type ?? '') == 'scooter' ? 'selected' : '' }}>Scooter</option>
                                <option value="bicycle" {{ ($rider->vehicle_type ?? '') == 'bicycle' ? 'selected' : '' }}>Bicycle</option>
                                <option value="car" {{ ($rider->vehicle_type ?? '') == 'car' ? 'selected' : '' }}>Car</option>
                            </select>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection