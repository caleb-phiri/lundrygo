<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>LaundryPro | Place New Order</title>
    <!-- Bootstrap 5 CSS + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #d4f1f9 0%, #b9e6f0 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        .bubble-bg {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.5) 0%, rgba(0,180,216,0.12) 100%);
            animation: floatBubble 22s infinite alternate ease-in-out;
        }

        .bubble-1 { width: 420px; height: 420px; top: -140px; left: -170px; animation-duration: 26s; }
        .bubble-2 { width: 550px; height: 550px; bottom: -200px; right: -200px; animation-duration: 32s; animation-delay: -5s; }
        .bubble-3 { width: 280px; height: 280px; top: 45%; left: 75%; animation-duration: 19s; animation-delay: -7s; }
        .bubble-4 { width: 180px; height: 180px; bottom: 15%; left: 10%; animation-duration: 24s; animation-delay: -3s; }

        @keyframes floatBubble {
            0% { transform: translate(0, 0) scale(1); opacity: 0.5; }
            100% { transform: translate(3%, 5%) scale(1.08); opacity: 0.85; }
        }

        .simple-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            position: relative;
            z-index: 10;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #00b4d8, #0284c7);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #00b4d8, #0284c7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }

        .dashboard-wrapper {
            position: relative;
            z-index: 5;
            padding: 2rem;
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 0.8rem;
            margin-bottom: 1.5rem;
        }

        .nav-card {
            background: white;
            border-radius: 1rem;
            padding: 0.8rem 0.5rem;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
        }

        .nav-card:hover {
            transform: translateY(-2px);
            border-color: #00b4d8;
            box-shadow: 0 6px 16px rgba(0,180,216,0.1);
        }

        .nav-card i {
            font-size: 1.4rem;
            color: #00b4d8;
            margin-bottom: 0.3rem;
            display: block;
        }

        .nav-card span {
            font-size: 0.75rem;
            font-weight: 600;
            color: #334155;
        }

        .nav-card.active {
            background: linear-gradient(135deg, #00b4d8, #0284c7);
            border-color: #00b4d8;
        }

        .nav-card.active i, .nav-card.active span {
            color: white;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(2px);
            border-radius: 2rem;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0, 100, 120, 0.3);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(115deg, #00b4d8, #0284c7);
            padding: 1.2rem 1.5rem;
            color: white;
        }

        .form-header h4 {
            font-weight: 800;
            margin: 0;
            font-size: 1.2rem;
        }

        .section-title {
            font-weight: 800;
            color: #02698b;
            font-size: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(0, 180, 216, 0.3);
            display: inline-block;
        }

        .form-control-fresh, .form-select-fresh {
            border-radius: 1.2rem;
            border: 1.5px solid #d4f0f5;
            padding: 0.7rem 1.2rem;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .form-control-fresh:focus, .form-select-fresh:focus {
            border-color: #00b4d8;
            box-shadow: 0 0 0 4px rgba(0, 180, 216, 0.15);
            outline: none;
        }

        .service-item {
            background: #f8fafc;
            border-radius: 1.2rem;
            padding: 1rem;
            margin-bottom: 0.8rem;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .service-item:hover {
            border-color: #00b4d8;
            background: white;
        }

        .summary-card {
            background: #f8fafc;
            border-radius: 1.2rem;
            padding: 1rem;
            border: 1px solid #e2e8f0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-row.total {
            font-weight: 800;
            font-size: 1.1rem;
            color: #0284c7;
            padding-top: 0.8rem;
            margin-top: 0.3rem;
            border-top: 2px solid #00b4d8;
        }

        .btn-primary-fresh {
            background: linear-gradient(105deg, #00b4d8, #0284c7);
            border: none;
            border-radius: 2rem;
            padding: 0.75rem 1.5rem;
            font-weight: 700;
            transition: all 0.25s;
            color: white;
        }

        .btn-primary-fresh:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 180, 216, 0.4);
        }

        .btn-outline-fresh {
            border: 1.5px solid #00b4d8;
            background: transparent;
            color: #0284c7;
            border-radius: 2rem;
            padding: 0.7rem 1.2rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-outline-fresh:hover {
            background: #00b4d8;
            color: white;
            transform: translateY(-1px);
        }

        .map-container {
            height: 250px;
            border-radius: 1.2rem;
            overflow: hidden;
            border: 2px solid #e2e8f0;
        }

        #locationMap {
            height: 100%;
            width: 100%;
        }

        .alert-fresh {
            border-radius: 1rem;
            background: #fff5f0;
            border-left: 4px solid #f97316;
            font-size: 0.85rem;
            padding: 0.8rem 1rem;
        }

        .alert-success-fresh {
            border-radius: 1rem;
            background: #d1fae5;
            border-left: 4px solid #10b981;
            font-size: 0.85rem;
            padding: 0.8rem 1rem;
            color: #065f46;
        }

        @media (max-width: 768px) {
            .dashboard-wrapper { padding: 1rem; }
            .nav-grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 576px) {
            .nav-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>

<div class="bubble-bg">
    <div class="bubble bubble-1"></div>
    <div class="bubble bubble-2"></div>
    <div class="bubble bubble-3"></div>
    <div class="bubble bubble-4"></div>
</div>

<div class="simple-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="logo"><i class="fas fa-soap me-1"></i> LaundryPro</span>
                <span class="ms-2 text-muted" style="font-size: 0.75rem;">place new order</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted d-none d-sm-inline" style="font-size: 0.85rem;">{{ auth()->user()->name ?? 'Customer' }}</span>
                <div class="user-avatar">
                    {{ substr(auth()->user()->name ?? 'C', 0, 1) }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-wrapper">
    <div class="container">
        <div class="nav-grid">
            <a href="{{ route('customer.dashboard') }}" class="nav-card">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('customer.orders') }}" class="nav-card">
                <i class="fas fa-box"></i>
                <span>My Orders</span>
            </a>
            <a href="{{ route('customer.orders.create') }}" class="nav-card active">
                <i class="fas fa-plus-circle"></i>
                <span>New Order</span>
            </a>
            <a href="{{ route('customer.addresses') }}" class="nav-card">
                <i class="fas fa-map-marker-alt"></i>
                <span>Addresses</span>
            </a>
            <a href="{{ route('customer.profile') }}" class="nav-card">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
            <a href="{{ route('customer.reviews') }}" class="nav-card">
                <i class="fas fa-star"></i>
                <span>Reviews</span>
            </a>
        </div>

        <div class="form-card">
            <div class="form-header">
                <h4><i class="fas fa-water me-2"></i> Place New Laundry Order</h4>
            </div>
            <div class="card-body p-4 p-md-5">
                
                @if($errors->any())
                    <div class="alert alert-fresh mb-4">
                        <i class="fas fa-circle-exclamation me-2"></i> Please fix the following:
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('customer.orders.store') }}" id="orderForm">
                    @csrf
                    
                    <div class="mb-4">
                        <h5 class="section-title"><i class="fas fa-truck-pickup me-2"></i> Pickup Address</h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <select name="pickup_address_id" id="pickup_address" class="form-select form-select-fresh @error('pickup_address_id') is-invalid @enderror" required>
                                    <option value="">Select Pickup Address</option>
                                    @foreach($addresses as $address)
                                        <option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }} data-lat="{{ $address->latitude ?? '-15.3875' }}" data-lng="{{ $address->longitude ?? '28.3228' }}">
                                            {{ $address->label }} - {{ $address->full_address ?? $address->address }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pickup_address_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('customer.addresses') }}" class="btn btn-outline-fresh w-100">
                                    <i class="fas fa-plus me-1"></i> New Address
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="section-title"><i class="fas fa-home me-2"></i> Delivery Address</h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <select name="delivery_address_id" id="delivery_address" class="form-select form-select-fresh @error('delivery_address_id') is-invalid @enderror" required>
                                    <option value="">Select Delivery Address</option>
                                    @foreach($addresses as $address)
                                        <option value="{{ $address->id }}" data-lat="{{ $address->latitude ?? '-15.3875' }}" data-lng="{{ $address->longitude ?? '28.3228' }}">
                                            {{ $address->label }} - {{ $address->full_address ?? $address->address }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('delivery_address_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('customer.addresses') }}" class="btn btn-outline-fresh w-100">
                                    <i class="fas fa-plus me-1"></i> New Address
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="section-title"><i class="fas fa-map-marked-alt me-2"></i> Location Preview</h5>
                        <div class="map-container">
                            <div id="locationMap"></div>
                        </div>
                        <input type="hidden" name="pickup_latitude" id="pickup_latitude" value="">
                        <input type="hidden" name="pickup_longitude" id="pickup_longitude" value="">
                        <p class="text-muted small mt-2"><i class="fas fa-info-circle me-1"></i> Interactive map showing selected address location</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="section-title"><i class="fas fa-tshirt me-2"></i> Select Services</h5>
                        <div id="services-container">
                            <div class="service-item">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold text-muted">Service Type</label>
                                        <select name="items[0][service_id]" class="form-select form-select-fresh service-select" required>
                                            <option value="">Select Service</option>
                                            @foreach($services->groupBy('category.name') as $category => $categoryServices)
                                                <optgroup label="{{ $category }}">
                                                    @foreach($categoryServices as $service)
                                                        <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-unit="{{ $service->unit }}">
                                                            {{ $service->name }} - ${{ number_format($service->price, 2) }}/{{ $service->unit }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted">Quantity</label>
                                        <input type="number" name="items[0][quantity]" class="form-control form-control-fresh quantity-input" placeholder="Qty" min="1" value="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted">Item Total</label>
                                        <input type="text" class="form-control form-control-fresh item-total" readonly placeholder="$0.00" style="background-color: #e9ecef;">
                                    </div>
                                    <div class="col-md-1 text-center">
                                        <button type="button" class="btn btn-danger remove-service" style="display: none; border-radius: 50%; width: 38px;">&times;</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-service" class="btn btn-outline-fresh btn-sm mt-3"><i class="fas fa-plus me-1"></i> Add Another Service</button>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <h5 class="section-title"><i class="fas fa-calendar-alt me-2"></i> Pickup Date & Time</h5>
                            <input type="datetime-local" name="pickup_date" class="form-control form-control-fresh @error('pickup_date') is-invalid @enderror" id="pickup_date" required>
                            @error('pickup_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <h5 class="section-title"><i class="fas fa-calendar-check me-2"></i> Delivery Date & Time</h5>
                            <input type="datetime-local" name="delivery_date" class="form-control form-control-fresh @error('delivery_date') is-invalid @enderror" id="delivery_date" required>
                            @error('delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="section-title"><i class="fas fa-pen-alt me-2"></i> Special Instructions</h5>
                        <textarea name="special_instructions" class="form-control form-control-fresh" rows="3" placeholder="Any special requests? e.g., fragile items, no starch, eco-friendly detergent, etc.">{{ old('special_instructions') }}</textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <h5 class="section-title"><i class="fas fa-tag me-2"></i> Promotion Code</h5>
                            <input type="text" name="promotion_code" id="promo_code" class="form-control form-control-fresh" placeholder="Enter promo code" value="{{ old('promotion_code') }}">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" id="apply-promo" class="btn btn-outline-fresh w-100"><i class="fas fa-gift me-1"></i> Apply Code</button>
                        </div>
                        <div id="promo-message" class="mt-2"></div>
                    </div>

                    <div class="mb-4">
                        <h5 class="section-title"><i class="fas fa-receipt me-2"></i> Order Summary</h5>
                        <div class="summary-card">
                            <div class="summary-row"><span>Subtotal:</span><span class="fw-bold">$<span id="subtotal">0.00</span></span></div>
                            <div class="summary-row"><span>Delivery Fee:</span><span>$<span id="delivery-fee">5.00</span></span></div>
                            <div class="summary-row"><span>Discount:</span><span class="text-danger">-$<span id="discount">0.00</span></span></div>
                            <div class="summary-row total"><span>Total:</span><span class="fs-5">$<span id="total">0.00</span></span></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="section-title"><i class="fas fa-credit-card me-2"></i> Payment Method</h5>
                        <div class="row g-3">
                            <div class="col-md-4"><div class="form-check"><input type="radio" name="payment_method" value="card" class="form-check-input" id="payment-card" checked><label class="form-check-label" for="payment-card"><i class="fas fa-credit-card me-1 text-primary"></i> Credit/Debit Card</label></div></div>
                            <div class="col-md-4"><div class="form-check"><input type="radio" name="payment_method" value="cash" class="form-check-input" id="payment-cash"><label class="form-check-label" for="payment-cash"><i class="fas fa-money-bill me-1 text-success"></i> Cash on Delivery</label></div></div>
                            <div class="col-md-4"><div class="form-check"><input type="radio" name="payment_method" value="wallet" class="form-check-input" id="payment-wallet"><label class="form-check-label" for="payment-wallet"><i class="fas fa-wallet me-1 text-info"></i> Wallet Balance</label></div></div>
                        </div>
                        @error('payment_method')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-3 justify-content-end mt-4">
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-fresh px-4"><i class="fas fa-times me-2"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary-fresh px-5" id="submitBtn"><i class="fas fa-check-circle me-2"></i> Place Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let serviceIndex = {{ isset($serviceIndex) ? $serviceIndex : 1 }};
    let currentDiscount = 0;
    const deliveryFee = 5.00;
    let map;
    let pickupMarker;

    function initMap() {
        const defaultLat = -15.3875;
        const defaultLng = 28.3228;
        map = L.map('locationMap').setView([defaultLat, defaultLng], 12);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);
        pickupMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map).bindPopup('Pickup Location').openPopup();
        pickupMarker.on('dragend', function(e) {
            const pos = pickupMarker.getLatLng();
            $('#pickup_latitude').val(pos.lat);
            $('#pickup_longitude').val(pos.lng);
        });
        map.on('click', function(e) {
            pickupMarker.setLatLng(e.latlng);
            $('#pickup_latitude').val(e.latlng.lat);
            $('#pickup_longitude').val(e.latlng.lng);
        });
    }

    function updateMapFromAddress(selectElement) {
        const selected = selectElement.find('option:selected');
        const lat = selected.data('lat');
        const lng = selected.data('lng');
        if (lat && lng && map) {
            map.setView([lat, lng], 14);
            pickupMarker.setLatLng([lat, lng]);
            $('#pickup_latitude').val(lat);
            $('#pickup_longitude').val(lng);
        }
    }

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
        initMap();
        calculateTotals();

        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        const minDateTime = now.toISOString().slice(0, 16);
        $('#pickup_date').attr('min', minDateTime);
        
        const deliveryMin = new Date();
        deliveryMin.setDate(deliveryMin.getDate() + 2);
        deliveryMin.setMinutes(deliveryMin.getMinutes() - deliveryMin.getTimezoneOffset());
        $('#delivery_date').attr('min', deliveryMin.toISOString().slice(0, 16));

        if ('{{ old("pickup_date") }}') $('#pickup_date').val('{{ old("pickup_date") }}');
        if ('{{ old("delivery_date") }}') $('#delivery_date').val('{{ old("delivery_date") }}');

        $('#pickup_address').change(function() { updateMapFromAddress($(this)); });
        if ($('#pickup_address').val()) updateMapFromAddress($('#pickup_address'));

        $('#add-service').click(function() {
            const newItem = $('.service-item:first').clone();
            newItem.find('.service-select').val('');
            newItem.find('.quantity-input').val(1);
            newItem.find('.item-total').val('');
            newItem.find('.remove-service').show();
            newItem.find('select, input').each(function() {
                const name = $(this).attr('name');
                if (name) $(this).attr('name', name.replace(/\[\d+\]/, '[' + serviceIndex + ']'));
            });
            $('#services-container').append(newItem);
            serviceIndex++;
            calculateTotals();
        });

        $(document).on('click', '.remove-service', function() {
            if ($('.service-item').length > 1) {
                $(this).closest('.service-item').fadeOut(200, function() { $(this).remove(); calculateTotals(); });
            } else { alert('At least one service is required'); }
        });

        $(document).on('change', '.service-select, .quantity-input', function() { calculateTotals(); });

        // Promotion validation using simple AJAX to same endpoint
        $('#apply-promo').click(function() {
            const code = $('#promo_code').val();
            const subtotal = parseFloat($('#subtotal').text());
            
            if (!code) {
                $('#promo-message').html('<div class="alert alert-info mt-2"><i class="fas fa-info-circle"></i> Enter a promotion code to save!</div>');
                setTimeout(() => $('#promo-message .alert').fadeOut(500), 3000);
                return;
            }
            
            // Send AJAX request to validate promotion via the same form action endpoint with a different approach
            $.ajax({
                url: '{{ route("customer.orders.store") }}?validate_promo=true',
                method: 'POST',
                data: {
                    promotion_code: code,
                    subtotal: subtotal,
                    _token: '{{ csrf_token() }}',
                    _validate_only: true
                },
                success: function(response) {
                    if (response.success) {
                        currentDiscount = response.discount;
                        $('#discount').text(currentDiscount.toFixed(2));
                        $('#promo-message').html('<div class="alert alert-success-fresh mt-2"><i class="fas fa-check-circle"></i> ' + response.message + '</div>');
                        calculateTotals();
                    } else {
                        currentDiscount = 0;
                        $('#discount').text('0.00');
                        $('#promo-message').html('<div class="alert alert-fresh mt-2"><i class="fas fa-exclamation-triangle"></i> ' + response.message + '</div>');
                        calculateTotals();
                    }
                },
                error: function(xhr) {
                    // Fallback client-side validation for demo
                    const promoCode = code.toUpperCase();
                    if (promoCode === 'WELCOME10') {
                        currentDiscount = subtotal * 0.10;
                        $('#discount').text(currentDiscount.toFixed(2));
                        $('#promo-message').html('<div class="alert alert-success-fresh mt-2"><i class="fas fa-check-circle"></i> 🎉 Promo code applied! 10% discount.</div>');
                    } else if (promoCode === 'SAVE20') {
                        currentDiscount = subtotal * 0.20;
                        $('#discount').text(currentDiscount.toFixed(2));
                        $('#promo-message').html('<div class="alert alert-success-fresh mt-2"><i class="fas fa-check-circle"></i> 🎉 Promo code applied! 20% discount.</div>');
                    } else if (promoCode === 'FREEDELIVERY') {
                        currentDiscount = deliveryFee;
                        $('#discount').text(currentDiscount.toFixed(2));
                        $('#promo-message').html('<div class="alert alert-success-fresh mt-2"><i class="fas fa-truck-fast"></i> 🚚 Free delivery applied!</div>');
                    } else {
                        currentDiscount = 0;
                        $('#discount').text('0.00');
                        $('#promo-message').html('<div class="alert alert-fresh mt-2"><i class="fas fa-exclamation-triangle"></i> Invalid promotion code. Try WELCOME10, SAVE20, or FREEDELIVERY</div>');
                    }
                    calculateTotals();
                }
            });
            
            setTimeout(() => { $('#promo-message .alert').fadeOut(500, function() { $(this).remove(); }); }, 4000);
        });

        $('#pickup_date').change(function() {
            const pickupDate = $(this).val();
            if (pickupDate) $('#delivery_date').attr('min', pickupDate);
        });

        const formCard = document.querySelector('.form-card');
        if (formCard) {
            formCard.style.opacity = '0';
            formCard.style.transform = 'translateY(20px)';
            setTimeout(() => {
                formCard.style.transition = 'all 0.5s cubic-bezier(0.2, 0.9, 0.4, 1.1)';
                formCard.style.opacity = '1';
                formCard.style.transform = 'translateY(0)';
            }, 100);
        }
    });
</script>

</body>
</html>