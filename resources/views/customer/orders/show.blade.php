@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action">My Orders</a>
                <a href="{{ route('customer.orders.create') }}" class="list-group-item list-group-item-action">New Order</a>
                <a href="{{ route('customer.addresses') }}" class="list-group-item list-group-item-action">Addresses</a>
                <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action">Profile</a>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Order #{{ $order->order_number }}</h4>
                    <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Order Date</h6>
                            <p>{{ $order->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Method</h6>
                            <p>{{ ucfirst($order->payment_method) }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Pickup Address</h6>
                            <p>{{ $order->pickupLocation->full_address ?? 'N/A' }}</p>
                            <small class="text-muted">Scheduled: {{ $order->pickup_scheduled_at ? $order->pickup_scheduled_at->format('F d, Y h:i A') : 'N/A' }}</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Delivery Address</h6>
                            <p>{{ $order->deliveryLocation->full_address ?? 'N/A' }}</p>
                            <small class="text-muted">Scheduled: {{ $order->delivery_scheduled_at ? $order->delivery_scheduled_at->format('F d, Y h:i A') : 'N/A' }}</small>
                        </div>
                    </div>
                    
                    <h6>Items</h6>
                    <table class="table table-bordered mb-4">
                        <thead>
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
                                <td>{{ $item->service_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                <td>${{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><td colspan="3" class="text-end"><strong>Subtotal</strong></td><td>${{ number_format($order->subtotal, 2) }}</td></tr>
                            @if($order->delivery_fee > 0)<tr><td colspan="3" class="text-end"><strong>Delivery Fee</strong></td><td>${{ number_format($order->delivery_fee, 2) }}</td></tr>@endif
                            @if($order->discount > 0)<tr><td colspan="3" class="text-end"><strong>Discount</strong></td><td>-${{ number_format($order->discount, 2) }}</td></tr>@endif
                            <tr class="fw-bold"><td colspan="3" class="text-end"><strong>Total</strong></td><td>${{ number_format($order->total, 2) }}</td></tr>
                        </tfoot>
                    </table>
                    
                    @if($order->special_instructions)
                    <div class="mb-4">
                        <h6>Special Instructions</h6>
                        <p>{{ $order->special_instructions }}</p>
                    </div>
                    @endif
                    
                    @if(in_array($order->status, ['pending', 'confirmed']))
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel Order</button>
                        <a href="{{ route('customer.orders.tracking', $order) }}" class="btn btn-info">Track Order</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(in_array($order->status, ['pending', 'confirmed']))
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('customer.orders.cancel', $order) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this order?</p>
                    <textarea name="reason" class="form-control" placeholder="Reason for cancellation (optional)" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Yes, Cancel Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection