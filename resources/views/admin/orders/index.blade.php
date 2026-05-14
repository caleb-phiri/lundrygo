@extends('layouts.app')

@section('title', 'Manage Orders')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-2">
            @include('admin.partials.sidebar')
        </div>
        
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Orders Management</h5>
                </div>
                <div class="card-body">
                    <!-- Stats Row -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="alert alert-primary">
                                <strong>Total Orders:</strong> {{ $stats['total'] }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-warning">
                                <strong>Pending:</strong> {{ $stats['pending'] }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-info">
                                <strong>Processing:</strong> {{ $stats['processing'] }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-success">
                                <strong>Delivered:</strong> {{ $stats['delivered'] }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Orders Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->order_number ?? $order->id }}</td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $order->status == 'delivered' ? 'success' :
                                            ($order->status == 'cancelled' ? 'danger' :
                                            ($order->status == 'processing' ? 'info' : 'warning'))
                                        }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection