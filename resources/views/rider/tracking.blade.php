@extends('layouts.app')

@section('title', 'Live Tracking')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3">
            @include('rider.partials.sidebar')
        </div>
        
        <div class="col-md-9">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Live Order Tracking</h5>
                </div>
                <div class="card-body">
                    @if($activeOrders->count() > 0)
                        <div class="row">
                            @foreach($activeOrders as $order)
                            <div class="col-md-6 mb-3">
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <h6>Order #{{ $order->order_number }}</h6>
                                        <p class="text-muted small">
                                            <i class="fas fa-map-marker-alt"></i> 
                                            {{ $order->deliveryAddress->address ?? 'Address' }}
                                        </p>
                                        <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                                        <a href="{{ route('rider.orders.show', $order) }}" class="btn btn-sm btn-primary mt-2">
                                            <i class="fas fa-map"></i> Track
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-map-marked-alt fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No active deliveries to track.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection