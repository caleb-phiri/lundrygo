@extends('layouts.app')

@section('title', 'Rider Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3">
            @include('rider.partials.sidebar')
        </div>
        
        <div class="col-md-9">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Total Deliveries</h6>
                                    <h2 class="mb-0">{{ $stats['total_deliveries'] }}</h2>
                                </div>
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Completed</h6>
                                    <h2 class="mb-0">{{ $stats['completed_deliveries'] }}</h2>
                                </div>
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Active Orders</h6>
                                    <h2 class="mb-0">{{ $stats['pending_deliveries'] }}</h2>
                                </div>
                                <i class="fas fa-truck fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Total Earnings</h6>
                                    <h2 class="mb-0">${{ number_format($stats['total_earnings'], 2) }}</h2>
                                </div>
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Available Orders -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Available Orders for Pickup</h5>
                </div>
                <div class="card-body">
                    @if($availableOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Pickup Location</th>
                                        <th>Delivery Location</th>
                                        <th>Distance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableOrders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        <td>
                                            <small>
                                                {{ $order->pickupAddress->address ?? $order->pickup_address_id }}
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                {{ $order->deliveryAddress->address ?? $order->delivery_address_id }}
                                            </small>
                                        </td>
                                        <td><span class="badge bg-secondary">Calculate</span></td>
                                        <td>
                                            <form action="{{ route('rider.orders.accept', $order) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Accept
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No available orders at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Active Orders -->
            @if($activeOrders->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Your Active Orders</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($activeOrders as $order)
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">Order #{{ $order->order_number }}</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-map-marker-alt"></i> 
                                                {{ $order->deliveryAddress->address ?? 'Address' }}
                                            </p>
                                            <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                                        </div>
                                        <a href="{{ route('rider.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-arrow-right"></i> Navigate
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection