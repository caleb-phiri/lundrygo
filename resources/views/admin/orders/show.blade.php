@extends('layouts.app')

@section('title', 'Order Details #' . ($order->order_number ?? $order->id))

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-2">
            @include('admin.partials.sidebar')
        </div>
        
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order #{{ $order->order_number ?? $order->id }}</h5>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Order Info -->
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <form action="{{ route('admin.orders.update-status', $order->id) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                <tr><th>Date:</th><td>{{ $order->created_at->format('F j, Y g:i A') }}</td></tr>
                                <tr><th>Total:</th><td>${{ number_format($order->total, 2) }}</td></tr>
                            </table>
                        </div>
                        
                        <!-- Customer Info -->
                        <div class="col-md-6">
                            <h6>Customer Information</h6>
                            <table class="table table-sm">
                                <tr><th>Name:</th><td>{{ $order->user->name ?? 'N/A' }}</td></tr>
                                <tr><th>Email:</th><td>{{ $order->user->email ?? 'N/A' }}</td></tr>
                                <tr><th>Phone:</th><td>{{ $order->user->phone ?? 'N/A' }}</td></tr>
                            </table>
                        </div>
                    </div>
                    <!-- Add this after the Customer Information section -->
<div class="row mt-3">
    <div class="col-md-12">
        <h6>Rider Information</h6>
        @if($order->rider)
            <div class="alert alert-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong><i class="fas fa-motorcycle"></i> Assigned Rider:</strong>
                        <div class="mt-2">
                            <strong>Name:</strong> {{ $order->rider->name }}<br>
                            <strong>Email:</strong> {{ $order->rider->email }}<br>
                            @if($order->rider->phone)
                            <strong>Phone:</strong> {{ $order->rider->phone }}
                            @endif
                        </div>
                        @if($order->rider_assigned_at)
                        <small class="text-muted">Assigned on: {{ $order->rider_assigned_at->format('F j, Y g:i A') }}</small>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('admin.orders.assign-rider', $order) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-sync-alt"></i> Change Rider
                        </a>
                        <a href="{{ route('admin.orders.unassign-rider', $order) }}" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to unassign this rider?')">
                            <i class="fas fa-user-times"></i> Unassign
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-exclamation-triangle"></i> No rider assigned to this order yet.
                    </div>
                    <a href="{{ route('admin.orders.assign-rider', $order) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus"></i> Assign Rider
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
                    <!-- Order Items -->
                    <h6 class="mt-3">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Service</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->service_name ?? $item->item_name ?? 'Service #' . $item->service_id }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price ?? 0, 2) }}</td>
                                    <td>${{ number_format(($item->unit_price ?? 0) * $item->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-active">
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal:</th>
                                    <th>${{ number_format($order->subtotal ?? 0, 2) }}</th>
                                </tr>
                                @if($order->delivery_fee > 0)
                                <tr>
                                    <th colspan="3" class="text-end">Delivery Fee:</th>
                                    <th>${{ number_format($order->delivery_fee ?? 0, 2) }}</th>
                                </tr>
                                @endif
                                @if(($order->discount ?? 0) > 0)
                                <tr>
                                    <th colspan="3" class="text-end">Discount:</th>
                                    <th>-${{ number_format($order->discount ?? 0, 2) }}</th>
                                </tr>
                                @endif
                                <tr class="table-primary">
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th><strong>${{ number_format($order->total ?? 0, 2) }}</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Address Information -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6>Pickup Address</h6>
                            <p>{{ $order->pickupAddress->full_address ?? $order->pickup_address_id ?? 'N/A' }}</p>
                            <small class="text-muted">Scheduled: {{ $order->pickup_scheduled_at ? $order->pickup_scheduled_at->format('F j, Y g:i A') : 'N/A' }}</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Delivery Address</h6>
                            <p>{{ $order->deliveryAddress->full_address ?? $order->delivery_address_id ?? 'N/A' }}</p>
                            <small class="text-muted">Scheduled: {{ $order->delivery_scheduled_at ? $order->delivery_scheduled_at->format('F j, Y g:i A') : 'N/A' }}</small>
                        </div>
                    </div>
                    
                    @if($order->special_instructions)
                    <div class="mt-3">
                        <h6>Special Instructions</h6>
                        <p>{{ $order->special_instructions }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection