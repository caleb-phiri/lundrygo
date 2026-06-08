@extends('layouts.app')

@section('title', 'Assign Rider - Order #' . ($order->order_number ?? $order->id))

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-2">
            @include('admin.partials.sidebar')
        </div>
        
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Assign Rider to Order #{{ $order->order_number ?? $order->id }}</h5>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Order
                    </a>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Customer:</strong> {{ $order->user->name ?? 'N/A' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Total Amount:</strong> ${{ number_format($order->total, 2) }}
                            </div>
                            <div class="col-md-4">
                                <strong>Current Status:</strong> 
                                <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current Rider -->
                    @if($currentRider)
                    <div class="alert alert-success mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Currently Assigned Rider:</strong>
                                <div class="mt-2">
                                    <i class="fas fa-motorcycle"></i> {{ $currentRider->name }}
                                    <br>
                                    <small>{{ $currentRider->email }}</small>
                                    @if($currentRider->phone)
                                    <br>
                                    <small><i class="fas fa-phone"></i> {{ $currentRider->phone }}</small>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('admin.orders.unassign-rider', $order) }}" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to unassign this rider?')">
                                <i class="fas fa-user-times"></i> Unassign Rider
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Assign New Rider Form -->
                   <form method="POST" action="{{ route('admin.orders.assign-rider', $order) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Rider</label>
                            <select name="rider_id" class="form-select" required>
                                <option value="">-- Select a Rider --</option>
                                @foreach($availableRiders as $rider)
                                <option value="{{ $rider->id }}" 
                                    {{ $currentRider && $currentRider->id == $rider->id ? 'selected' : '' }}
                                    data-active-orders="{{ $rider->active_orders ?? 0 }}">
                                    {{ $rider->name }} - {{ $rider->email }}
                                    @if(isset($rider->active_orders))
                                    ({{ $rider->active_orders }} active orders)
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select a rider to assign to this order</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Assignment Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Add any special instructions for the rider..."></textarea>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i> 
                            When you assign a rider, the order status will automatically change to "Confirmed" if it's still pending.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Assign Rider
                            </button>
                        </div>
                    </form>
                    
                    <!-- Available Riders List -->
                    <div class="mt-4">
                        <h6>Available Riders</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Active Orders</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($availableRiders as $rider)
                                    <tr>
                                        <td>{{ $rider->name }}</td>
                                        <td>{{ $rider->email }}</td>
                                        <td>{{ $rider->phone ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ ($rider->active_orders ?? 0) > 3 ? 'danger' : (($rider->active_orders ?? 0) > 0 ? 'warning' : 'success') }}">
                                                {{ $rider->active_orders ?? 0 }} orders
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No riders available</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('select[name="rider_id"]').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const activeOrders = selectedOption.dataset.activeOrders;
    
    if (activeOrders && parseInt(activeOrders) > 5) {
        alert('Warning: This rider already has ' + activeOrders + ' active orders. Consider selecting another rider.');
    }
});
</script>
@endsection