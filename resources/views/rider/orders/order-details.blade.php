@extends('layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3">
            @include('rider.partials.sidebar')
        </div>
        
        <div class="col-md-9">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                    <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <!-- Order Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Customer Information</h6>
                            <p>
                                <strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}<br>
                                <strong>Phone:</strong> {{ $order->user->phone ?? 'N/A' }}<br>
                                <strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Order Summary</h6>
                            <p>
                                <strong>Order Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}<br>
                                <strong>Total Amount:</strong> ${{ number_format($order->total, 2) }}<br>
                                <strong>Delivery Fee:</strong> ${{ number_format($order->delivery_fee, 2) }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Addresses -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <strong><i class="fas fa-home"></i> Pickup Address:</strong><br>
                                {{ $order->pickupAddress->address ?? $order->pickup_address_id ?? 'N/A' }}
                                @if($order->pickup_scheduled_at)
                                <br><small>Scheduled: {{ $order->pickup_scheduled_at->format('M d, Y h:i A') }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <strong><i class="fas fa-map-marker-alt"></i> Delivery Address:</strong><br>
                                {{ $order->deliveryAddress->address ?? $order->delivery_address_id ?? 'N/A' }}
                                @if($order->delivery_scheduled_at)
                                <br><small>Scheduled: {{ $order->delivery_scheduled_at->format('M d, Y h:i A') }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <h6>Order Items</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->service_name ?? 'Service #' . $item->service_id }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price ?? 0, 2) }}</td>
                                    <td>${{ number_format(($item->unit_price ?? 0) * $item->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal:</th>
                                    <th>${{ number_format($order->subtotal, 2) }}</th>
                                </tr>
                                @if($order->delivery_fee > 0)
                                <tr>
                                    <th colspan="3" class="text-end">Delivery Fee:</th>
                                    <th>${{ number_format($order->delivery_fee, 2) }}</th>
                                </tr>
                                @endif
                                @if($order->discount > 0)
                                <tr>
                                    <th colspan="3" class="text-end">Discount:</th>
                                    <th>-${{ number_format($order->discount, 2) }}</th>
                                </tr>
                                @endif
                                <tr class="table-primary">
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th><strong>${{ number_format($order->total, 2) }}</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-3">
                        @if($order->status == 'pending' || $order->status == 'confirmed')
                            <form action="{{ route('rider.orders.accept', $order) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Accept Order
                                </button>
                            </form>
                        @endif
                        
                        @if($order->status == 'processing' || $order->status == 'ready_for_delivery')
                            <form action="{{ route('rider.orders.pickup', $order) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-box"></i> Mark as Picked Up
                                </button>
                            </form>
                        @endif
                        
                        @if($order->status == 'out_for_delivery')
                            <form action="{{ route('rider.orders.deliver', $order) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Confirm delivery?')">
                                    <i class="fas fa-check-circle"></i> Mark as Delivered
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('rider.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection