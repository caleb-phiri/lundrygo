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
                    
                    <!-- Order Items -->
                    <h6 class="mt-3">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
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
                                    <td>{{ $item->service->name ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th>${{ number_format($order->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection