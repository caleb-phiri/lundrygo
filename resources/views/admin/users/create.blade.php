@extends('layouts.app')

@section('title', 'Place New Order')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-3x"></i>
                        </div>
                    </div>
                    <h5>{{ auth()->user()->name }}</h5>
                    <p class="text-muted small">{{ auth()->user()->email }}</p>
                    <hr>
                    <div class="row text-start">
                        <div class="col-6">
                            <small class="text-muted">Active Orders</small>
                            <h6 class="mb-0">{{ $activeOrders ?? 0 }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Completed</small>
                            <h6 class="mb-0">{{ $completedOrders ?? 0 }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="list-group">
                <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-box me-2"></i> My Orders
                </a>
                <a href="{{ route('customer.orders.create') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-plus-circle me-2"></i> New Order
                </a>
                <a href="{{ route('customer.addresses') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-map-marker-alt me-2"></i> Addresses
                </a>
                <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user me-2"></i> Profile
                </a>
                <a href="{{ route('customer.reviews') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-star me-2"></i> Reviews
                </a>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Place New Laundry Order</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('customer.orders.store') }}" id="orderForm">
                        @csrf
                        
                        <!-- Pickup Address -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pickup Address <span class="text-danger">*</span></label>
                            <select name="pickup_address_id" id="pickup_address_id" class="form-select" required>
                                <option value="">Select Pickup Address</option>
                                @foreach($addresses as $address)
                                    <option value="{{ $address->id }}" 
                                            data-lat="{{ $address->latitude ?? '-15.3875' }}"
                                            data-lng="{{ $address->longitude ?? '28.3228' }}"
                                            {{ old('pickup_address_id') == $address->id ? 'selected' : ($address->is_default ? 'selected' : '') }}>
                                        {{ $address->label }} - {{ $address->full_address ?? $address->street_address }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="pickup_latitude" id="pickup_latitude" value="{{ old('pickup_latitude') }}">
                            <input type="hidden" name="pickup_longitude" id="pickup_longitude" value="{{ old('pickup_longitude') }}">
                        </div>
                        
                        <!-- Delivery Address -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Delivery Address <span class="text-danger">*</span></label>
                            <select name="delivery_address_id" id="delivery_address_id" class="form-select" required>
                                <option value="">Select Delivery Address</option>
                                @foreach($addresses as $address)
                                    <option value="{{ $address->id }}" 
                                            data-lat="{{ $address->latitude ?? '-15.3875' }}"
                                            data-lng="{{ $address->longitude ?? '28.3228' }}"
                                            {{ old('delivery_address_id') == $address->id ? 'selected' : '' }}>
                                        {{ $address->label }} - {{ $address->full_address ?? $address->street_address }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="delivery_latitude" id="delivery_latitude" value="{{ old('delivery_latitude') }}">
                            <input type="hidden" name="delivery_longitude" id="delivery_longitude" value="{{ old('delivery_longitude') }}">
                        </div>
                        
                        <!-- Services -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Services <span class="text-danger">*</span></label>
                            <div id="services-container">
                                <div class="service-item row g-3 mb-2 align-items-end">
                                    <div class="col-md-6">
                                        <select name="items[0][service_id]" class="form-select service-select" required>
                                            <option value="">Select Service</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                                    {{ $service->name }} - ${{ number_format($service->price, 2) }}/{{ $service->unit ?? 'item' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="items[0][quantity]" class="form-control quantity-input" placeholder="Qty" min="1" value="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control item-total" readonly placeholder="$0.00" style="background:#e9ecef">
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-service" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-plus"></i> Add Another Service
                            </button>
                        </div>
                        
                        <!-- Schedule -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pickup Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="pickup_date" id="pickup_date" class="form-control" value="{{ old('pickup_date') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Delivery Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="delivery_date" id="delivery_date" class="form-control" value="{{ old('delivery_date') }}" required>
                            </div>
                        </div>
                        
                        <!-- Special Instructions -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Special Instructions</label>
                            <textarea name="special_instructions" class="form-control" rows="3" placeholder="Any special requests?">{{ old('special_instructions') }}</textarea>
                        </div>
                        
                        <!-- Promotion Code -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Promotion Code</label>
                            <div class="input-group">
                                <input type="text" name="promotion_code" id="promo_code" class="form-control" value="{{ old('promotion_code') }}" placeholder="Enter promo code">
                                <button type="button" id="apply-promo" class="btn btn-outline-primary">Apply</button>
                            </div>
                            <div id="promo-message" class="mt-2"></div>
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Order Summary</label>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>Subtotal:</td>
                                        <td class="text-end">$<span id="subtotal">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td>Delivery Fee:</td>
                                        <td class="text-end">$<span id="delivery_fee">5.00</span></td>
                                    </tr>
                                    <tr>
                                        <td>Discount:</td>
                                        <td class="text-end text-danger">-$<span id="discount">0.00</span></td>
                                    </tr>
                                    <tr class="table-active">
                                        <td class="fw-bold">Total:</td>
                                        <td class="text-end fw-bold">$<span id="total">0.00</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Payment Method -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="radio" name="payment_method" value="card" class="form-check-input" id="payment_card" checked>
                                        <label class="form-check-label" for="payment_card">Credit/Debit Card</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="radio" name="payment_method" value="cash" class="form-check-input" id="payment_cash">
                                        <label class="form-check-label" for="payment_cash">Cash on Delivery</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="radio" name="payment_method" value="wallet" class="form-check-input" id="payment_wallet">
                                        <label class="form-check-label" for="payment_wallet">Wallet Balance</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Place Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let serviceIndex = 1;
let currentDiscount = 0;
let deliveryFee = 5.00;

function calculateTotals() {
    let subtotal = 0;
    $('.service-item').each(function() {
        const price = parseFloat($(this).find('.service-select option:selected').data('price') || 0);
        const quantity = parseInt($(this).find('.quantity-input').val() || 0);
        const total = price * quantity;
        $(this).find('.item-total').val('$' + total.toFixed(2));
        subtotal += total;
    });
    $('#subtotal').text(subtotal.toFixed(2));
    let total = subtotal + deliveryFee - currentDiscount;
    if (total < 0) total = 0;
    $('#total').text(total.toFixed(2));
}

$(document).ready(function() {
    calculateTotals();
    
    // Set default dates
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    const minDateTime = now.toISOString().slice(0, 16);
    $('#pickup_date').attr('min', minDateTime);
    if (!$('#pickup_date').val()) $('#pickup_date').val(minDateTime);
    
    const deliveryMin = new Date();
    deliveryMin.setDate(deliveryMin.getDate() + 1);
    deliveryMin.setMinutes(deliveryMin.getMinutes() - deliveryMin.getTimezoneOffset());
    $('#delivery_date').attr('min', deliveryMin.toISOString().slice(0, 16));
    
    // Set coordinates when address changes
    $('#pickup_address_id').change(function() {
        const selected = $(this).find('option:selected');
        const lat = selected.data('lat');
        const lng = selected.data('lng');
        if (lat && lng) {
            $('#pickup_latitude').val(lat);
            $('#pickup_longitude').val(lng);
        }
    });
    
    $('#delivery_address_id').change(function() {
        const selected = $(this).find('option:selected');
        const lat = selected.data('lat');
        const lng = selected.data('lng');
        if (lat && lng) {
            $('#delivery_latitude').val(lat);
            $('#delivery_longitude').val(lng);
        }
    });
    
    // Trigger initial coordinate setting
    $('#pickup_address_id').trigger('change');
    $('#delivery_address_id').trigger('change');
    
    // Add service
    $('#add-service').click(function() {
        const newItem = $('.service-item:first').clone();
        newItem.find('.service-select').val('');
        newItem.find('.quantity-input').val(1);
        newItem.find('.item-total').val('');
        newItem.find('select, input').each(function() {
            const name = $(this).attr('name');
            if (name) $(this).attr('name', name.replace(/\[\d+\]/, '[' + serviceIndex + ']'));
        });
        $('#services-container').append(newItem);
        serviceIndex++;
        calculateTotals();
    });
    
    // Remove service
    $(document).on('click', '.remove-service', function() {
        if ($('.service-item').length > 1) {
            $(this).closest('.service-item').remove();
            calculateTotals();
        } else {
            alert('At least one service is required');
        }
    });
    
    // Add remove button to first service
    $('.service-item:first .col-md-3:last').append('<button type="button" class="btn btn-sm btn-danger remove-service ms-2" style="height: 38px;"><i class="fas fa-trash"></i></button>');
    
    // Calculate on change
    $(document).on('change', '.service-select, .quantity-input', function() {
        calculateTotals();
    });
    
    // Apply promo
    $('#apply-promo').click(function() {
        const code = $('#promo_code').val();
        const subtotal = parseFloat($('#subtotal').text());
        
        if (!code) {
            $('#promo-message').html('<div class="alert alert-info">Enter a promotion code</div>');
            setTimeout(() => $('#promo-message').empty(), 3000);
            return;
        }
        
        const promoCode = code.toUpperCase();
        if (promoCode === 'WELCOME10') {
            currentDiscount = subtotal * 0.10;
            $('#discount').text(currentDiscount.toFixed(2));
            $('#promo-message').html('<div class="alert alert-success">🎉 10% discount applied!</div>');
        } else if (promoCode === 'SAVE20') {
            currentDiscount = subtotal * 0.20;
            $('#discount').text(currentDiscount.toFixed(2));
            $('#promo-message').html('<div class="alert alert-success">🎉 20% discount applied!</div>');
        } else if (promoCode === 'FREEDELIVERY') {
            currentDiscount = deliveryFee;
            $('#discount').text(currentDiscount.toFixed(2));
            $('#promo-message').html('<div class="alert alert-success">🚚 Free delivery applied!</div>');
        } else {
            currentDiscount = 0;
            $('#discount').text('0.00');
            $('#promo-message').html('<div class="alert alert-danger">Invalid promo code</div>');
        }
        calculateTotals();
        setTimeout(() => $('#promo-message').empty(), 3000);
    });
    
    // Validate pickup date doesn't exceed delivery date
    $('#pickup_date').change(function() {
        const pickupDate = $(this).val();
        if (pickupDate) $('#delivery_date').attr('min', pickupDate);
    });
    
    // Form validation
    $('#orderForm').on('submit', function(e) {
        if (!$('#pickup_address_id').val()) {
            e.preventDefault();
            alert('Please select a pickup address');
            return false;
        }
        if (!$('#delivery_address_id').val()) {
            e.preventDefault();
            alert('Please select a delivery address');
            return false;
        }
        
        let hasService = false;
        $('.service-select').each(function() {
            if ($(this).val()) hasService = true;
        });
        if (!hasService) {
            e.preventDefault();
            alert('Please select at least one service');
            return false;
        }
        
        // Set default coordinates if not set
        if (!$('#pickup_latitude').val()) $('#pickup_latitude').val('-15.3875');
        if (!$('#pickup_longitude').val()) $('#pickup_longitude').val('28.3228');
        if (!$('#delivery_latitude').val()) $('#delivery_latitude').val('-15.3875');
        if (!$('#delivery_longitude').val()) $('#delivery_longitude').val('28.3228');
        
        return true;
    });
});
</script>

<style>
.service-item {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
}
.remove-service {
    margin-top: 0;
}
</style>
@endsection