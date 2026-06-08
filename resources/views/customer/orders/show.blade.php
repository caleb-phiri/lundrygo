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
                    <!-- Rider Information Section -->
                    @if($order->rider)
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-motorcycle fa-2x me-3"></i>
                            <div>
                                <strong class="d-block">Your Rider:</strong>
                                {{ $order->rider->name }}
                                @if($order->rider->phone)
                                <br><small><i class="fas fa-phone"></i> {{ $order->rider->phone }}</small>
                                @endif
                                @if($order->rider_assigned_at)
                                <br><small class="text-muted">Assigned on: {{ \Carbon\Carbon::parse($order->rider_assigned_at)->format('F d, Y h:i A') }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-secondary mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock fa-2x me-3"></i>
                            <div>
                                <strong>Looking for a rider...</strong>
                                <br><small>A rider will be assigned to your order soon.</small>
                            </div>
                        </div>
                    </div>
                    @endif
                    
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
                            <p>{{ $order->pickupAddress->full_address ?? $order->pickup_address_id ?? 'N/A' }}</p>
                            <small class="text-muted">Scheduled: {{ $order->pickup_scheduled_at ? $order->pickup_scheduled_at->format('F d, Y h:i A') : 'N/A' }}</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Delivery Address</h6>
                            <p>{{ $order->deliveryAddress->full_address ?? $order->delivery_address_id ?? 'N/A' }}</p>
                            <small class="text-muted">Scheduled: {{ $order->delivery_scheduled_at ? $order->delivery_scheduled_at->format('F d, Y h:i A') : 'N/A' }}</small>
                        </div>
                    </div>
                    
                    <h6>Items</h6>
                    <div class="table-responsive">
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
                    
                    @if($order->special_instructions)
                    <div class="mb-4">
                        <h6>Special Instructions</h6>
                        <p>{{ $order->special_instructions }}</p>
                    </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 mt-3">
                        @if(in_array($order->status, ['pending', 'confirmed']))
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            Cancel Order
                        </button>
                        @endif
                        
                        @if($order->rider && $order->status != 'delivered')
                        <a href="{{ route('customer.orders.tracking', $order) }}" class="btn btn-info">
                            <i class="fas fa-map-marker-alt"></i> Track Order
                        </a>
                        @endif
                        
                        @if($order->status == 'delivered')
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            <i class="fas fa-star"></i> Leave a Review
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
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
                    <p>Are you sure you want to cancel order #{{ $order->order_number }}?</p>
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

<!-- Review Modal -->
@if($order->status == 'delivered')
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('customer.reviews.store', $order) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Review Your Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <div class="rating">
                            <input type="radio" name="rating" value="5" id="star5"><label for="star5">★</label>
                            <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
                            <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
                            <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
                            <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Review</label>
                        <textarea name="review" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.rating input {
    display: none;
}
.rating label {
    font-size: 30px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}
.rating input:checked ~ label,
.rating label:hover,
.rating label:hover ~ label {
    color: #ffc107;
}
</style>
@endsection