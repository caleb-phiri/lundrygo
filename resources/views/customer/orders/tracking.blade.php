@extends('layouts.app')

@section('title', 'Track Order #' . ($order->order_number ?? $order->id))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-box me-2"></i> My Orders
                </a>
                <a href="{{ route('customer.orders.create') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-plus-circle me-2"></i> New Order
                </a>
                <a href="{{ route('customer.addresses') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-map-marker-alt me-2"></i> Addresses
                </a>
                <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user me-2"></i> Profile
                </a>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Track Order #{{ $order->order_number ?? $order->id }}</h4>
                    <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }} fs-6">
                        {{ $order->status_label ?? ucfirst($order->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <!-- Progress Bar -->
                    <div class="text-center mb-4">
                        <h6 class="mb-3">Order Progress</h6>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: {{ $progress ?? $order->delivery_progress_percentage ?? 0 }}%" 
                                 aria-valuenow="{{ $progress ?? $order->delivery_progress_percentage ?? 0 }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $progress ?? $order->delivery_progress_percentage ?? 0 }}%
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tracking Timeline -->
                    <div class="timeline mt-4">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="list-unstyled">
                                    <!-- Order Placed -->
                                    <li class="mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <i class="fas {{ $order->created_at ? 'fa-check-circle text-success' : 'fa-circle text-muted' }} fa-2x"></i>
                                            </div>
                                            <div>
                                                <strong>Order Placed</strong><br>
                                                <small class="text-muted">{{ $order->created_at ? $order->created_at->format('F d, Y h:i A') : 'Pending' }}</small>
                                            </div>
                                        </div>
                                    </li>
                                    
                                    <!-- Order Confirmed -->
                                    <li class="mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <i class="fas {{ $order->order_confirmed_at || in_array($order->status, ['confirmed', 'processing', 'delivered']) ? 'fa-check-circle text-success' : 'fa-circle text-muted' }} fa-2x"></i>
                                            </div>
                                            <div>
                                                <strong>Order Confirmed</strong><br>
                                                <small class="text-muted">{{ $order->order_confirmed_at ? $order->order_confirmed_at->format('F d, Y h:i A') : ($order->created_at ? 'Processing' : 'Pending') }}</small>
                                            </div>
                                        </div>
                                    </li>
                                    
                                    <!-- Picked Up -->
                                    <li class="mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <i class="fas {{ $order->picked_up_at ? 'fa-check-circle text-success' : 'fa-circle text-muted' }} fa-2x"></i>
                                            </div>
                                            <div>
                                                <strong>Picked Up</strong><br>
                                                <small class="text-muted">{{ $order->picked_up_at ? $order->picked_up_at->format('F d, Y h:i A') : 'Awaiting Pickup' }}</small>
                                            </div>
                                        </div>
                                    </li>
                                    
                                    <!-- Processing -->
                                    <li class="mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <i class="fas {{ $order->processing_started_at ? 'fa-check-circle text-success' : 'fa-circle text-muted' }} fa-2x"></i>
                                            </div>
                                            <div>
                                                <strong>Processing at Laundry</strong><br>
                                                <small class="text-muted">{{ $order->processing_started_at ? $order->processing_started_at->format('F d, Y h:i A') : 'Pending' }}</small>
                                            </div>
                                        </div>
                                    </li>
                                    
                                    <!-- Out for Delivery -->
                                    <li class="mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <i class="fas {{ $order->out_for_delivery_at ? 'fa-check-circle text-success' : 'fa-circle text-muted' }} fa-2x"></i>
                                            </div>
                                            <div>
                                                <strong>Out for Delivery</strong><br>
                                                <small class="text-muted">{{ $order->out_for_delivery_at ? $order->out_for_delivery_at->format('F d, Y h:i A') : 'Pending' }}</small>
                                            </div>
                                        </div>
                                    </li>
                                    
                                    <!-- Delivered -->
                                    <li class="mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <i class="fas {{ $order->delivered_at ? 'fa-check-circle text-success' : 'fa-circle text-muted' }} fa-2x"></i>
                                            </div>
                                            <div>
                                                <strong>Delivered</strong><br>
                                                <small class="text-muted">{{ $order->delivered_at ? $order->delivered_at->format('F d, Y h:i A') : 'Pending' }}</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Information -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6>Order Details</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Order Number:</th>
                                    <td>{{ $order->order_number ?? $order->id }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>{{ $order->status_label ?? ucfirst($order->status) }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Status:</th>
                                    <td>{{ $order->payment_status_label ?? ucfirst($order->payment_status ?? 'Pending') }}</td>
                                </tr>
                                <tr>
                                    <th>Total Amount:</th>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Schedule</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Pickup Date:</th>
                                    <td>{{ $order->pickup_scheduled_at ? $order->pickup_scheduled_at->format('F d, Y h:i A') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Delivery Date:</th>
                                    <td>{{ $order->delivery_scheduled_at ? $order->delivery_scheduled_at->format('F d, Y h:i A') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="text-center mt-4">
                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Order Details
                        </a>
                        @if($order->status != 'delivered' && $order->status != 'cancelled')
                        <button type="button" class="btn btn-outline-info ms-2" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i> Refresh Status
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}
.timeline li {
    border-left: 2px solid #dee2e6;
    padding-left: 20px;
    position: relative;
}
.timeline li:before {
    content: '';
    position: absolute;
    left: -8px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #dee2e6;
}
.timeline li:last-child {
    border-left: none;
}
</style>
@endsection