@extends('layouts.app')

@section('title', 'My Deliveries')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3">
            @include('rider.partials.sidebar')
        </div>
        
        <div class="col-md-9">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">My Deliveries</h5>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Pickup Address</th>
                                        <th>Delivery Address</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        <td>{{ Str::limit($order->pickupAddress->address ?? 'N/A', 30) }}</td>
                                        <td>{{ Str::limit($order->deliveryAddress->address ?? 'N/A', 30) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('rider.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $orders->links() }}
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No deliveries yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection