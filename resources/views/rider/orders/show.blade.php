@extends('layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3">
            @include('rider.partials.sidebar')
        </div>
        
        <div class="col-md-9">
            <!-- Order Header -->
            <div class="card shadow mb-4">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-box me-2"></i> Order #{{ $order->order_number }}
                        </h5>
                        <small>Order placed on {{ $order->created_at->format('F d, Y h:i A') }}</small>
                    </div>
                    <div>
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'confirmed' => 'info',
                                'processing' => 'primary',
                                'ready_for_delivery' => 'secondary',
                                'out_for_delivery' => 'info',
                                'delivered' => 'success',
                                'cancelled' => 'danger'
                            ];
                            $statusColor = $statusColors[$order->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $statusColor }} fs-6 px-3 py-2">
                            <i class="fas fa-circle me-1"></i> {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Order Details Column -->
                <div class="col-lg-5">
                    <!-- Customer Information Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-user me-2 text-primary"></i> Customer Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $order->user->name ?? 'N/A' }}</h6>
                                    <small class="text-muted">{{ $order->user->email ?? 'N/A' }}</small>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <small class="text-muted d-block">Phone Number</small>
                                    <strong><i class="fas fa-phone me-2"></i> {{ $order->user->phone ?? 'Not provided' }}</strong>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Customer Since</small>
                                    <strong><i class="fas fa-calendar-alt me-2"></i> {{ $order->user->created_at ? $order->user->created_at->format('M d, Y') : 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i> Order Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal:</span>
                                    <strong>${{ number_format($order->subtotal, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Delivery Fee:</span>
                                    <strong>${{ number_format($order->delivery_fee, 2) }}</strong>
                                </div>
                                @if($order->discount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Discount:</span>
                                    <strong class="text-danger">-${{ number_format($order->discount, 2) }}</strong>
                                </div>
                                @endif
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Total Amount:</span>
                                    <span class="fw-bold text-primary fs-5">${{ number_format($order->total, 2) }}</span>
                                </div>
                            </div>
                            
                            @if($order->special_instructions)
                            <hr>
                            <div class="mt-3">
                                <small class="text-muted d-block">Special Instructions</small>
                                <p class="mb-0 mt-1">{{ $order->special_instructions }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Order Items Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-shopping-bag me-2 text-primary"></i> Order Items</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td>{{ $item->service_name ?? 'Service #' . $item->service_id }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end">${{ number_format($item->unit_price ?? 0, 2) }}</td>
                                            <td class="text-end">${{ number_format(($item->unit_price ?? 0) * $item->quantity, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maps and Actions Column -->
                <div class="col-lg-7">
                    <!-- Location Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Delivery Locations</h6>
                        </div>
                        <div class="card-body">
                            <!-- Pickup Address -->
                            <div class="mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                        <i class="fas fa-home fa-sm"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong class="d-block">Pickup Address</strong>
                                        <p class="text-muted mb-2">{{ $order->pickupAddress->address ?? $order->pickupAddress->full_address ?? 'Address not provided' }}</p>
                                        @if($order->pickup_scheduled_at)
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> Scheduled: {{ $order->pickup_scheduled_at->format('M d, Y h:i A') }}
                                        </small>
                                        @endif
                                        <div class="mt-2">
                                            <a href="https://www.google.com/maps/dir/{{ $riderLocation ?? '' }}/{{ urlencode($order->pickupAddress->address ?? '') }}" 
                                               target="_blank" class="btn btn-sm btn-outline-success me-2">
                                                <i class="fas fa-directions"></i> Navigate to Pickup
                                            </a>
                                            <button onclick="centerMapOnPickup()" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-crosshairs"></i> Center Map
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Delivery Address -->
                            <div class="mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                        <i class="fas fa-flag-checkered fa-sm"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong class="d-block">Delivery Address</strong>
                                        <p class="text-muted mb-2">{{ $order->deliveryAddress->address ?? $order->deliveryAddress->full_address ?? 'Address not provided' }}</p>
                                        @if($order->delivery_scheduled_at)
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> Scheduled: {{ $order->delivery_scheduled_at->format('M d, Y h:i A') }}
                                        </small>
                                        @endif
                                        <div class="mt-2">
                                            <a href="https://www.google.com/maps/dir/{{ $riderLocation ?? '' }}/{{ urlencode($order->deliveryAddress->address ?? '') }}" 
                                               target="_blank" class="btn btn-sm btn-outline-danger me-2">
                                                <i class="fas fa-directions"></i> Navigate to Delivery
                                            </a>
                                            <button onclick="centerMapOnDelivery()" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-crosshairs"></i> Center Map
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Google Maps Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-map me-2 text-primary"></i> Live Location Tracking</h6>
                            <div>
                                <span id="liveIndicator" class="badge bg-success">
                                    <i class="fas fa-circle fa-xs me-1"></i> Live
                                </span>
                                <button onclick="refreshLocation()" class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="map" style="height: 450px; width: 100%;"></div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="row text-center">
                                <div class="col-4">
                                    <i class="fas fa-map-marker-alt text-success"></i>
                                    <small class="d-block">Pickup</small>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-motorcycle text-primary"></i>
                                    <small class="d-block">Your Location</small>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-flag-checkered text-danger"></i>
                                    <small class="d-block">Delivery</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons Card -->
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                @if($order->status == 'pending' || $order->status == 'confirmed')
                                    <form action="{{ route('rider.orders.accept', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success px-4 py-2">
                                            <i class="fas fa-check-circle me-2"></i> Accept Order
                                        </button>
                                    </form>
                                @endif
                                
                                @if($order->status == 'processing' || $order->status == 'ready_for_delivery')
                                    <form action="{{ route('rider.orders.pickup', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning px-4 py-2">
                                            <i class="fas fa-box me-2"></i> Mark as Picked Up
                                        </button>
                                    </form>
                                @endif
                                
                                @if($order->status == 'out_for_delivery')
                                    <form action="{{ route('rider.orders.deliver', $order) }}" method="POST" class="d-inline" onsubmit="return confirmDelivery()">
                                        @csrf
                                        <button type="submit" class="btn btn-success px-4 py-2">
                                            <i class="fas fa-check-circle me-2"></i> Mark as Delivered
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('rider.dashboard') }}" class="btn btn-secondary px-4 py-2">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </a>
                            </div>
                            
                            @if($order->status == 'delivered')
                            <div class="alert alert-success mt-3 mb-0 text-center">
                                <i class="fas fa-trophy me-2"></i> 
                                This order has been completed. Thank you for your service!
                            </div>
                            @endif
                            
                            @if($order->status == 'cancelled')
                            <div class="alert alert-danger mt-3 mb-0 text-center">
                                <i class="fas fa-ban me-2"></i> 
                                This order has been cancelled.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delivery Confirmation Modal -->
<div class="modal fade" id="deliveryConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Confirm Delivery</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-check-circle text-success fa-4x"></i>
                </div>
                <p class="text-center">Are you sure you want to mark this order as <strong>Delivered</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i> Please confirm that you have delivered all items to the customer.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmDeliverBtn">
                    <i class="fas fa-check"></i> Yes, Deliver Order
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .card {
        border: none;
        border-radius: 12px;
    }
    .card-header {
        border-bottom: 1px solid #eef2f6;
        border-radius: 12px 12px 0 0 !important;
    }
    .btn {
        border-radius: 8px;
    }
    .table td, .table th {
        padding: 12px;
    }
</style>
@endpush

@push('scripts')
<script>
    let map;
    let pickupMarker;
    let deliveryMarker;
    let riderMarker;
    let directionsService;
    let directionsRenderer;
    let updateInterval;
    
    // Coordinates - Replace with actual lat/lng from your database
    const pickupLocation = { lat: -15.3875, lng: 28.3228 };
    const deliveryLocation = { lat: -15.4167, lng: 28.2833 };
    let riderLocation = { lat: -15.3875, lng: 28.3228 };
    let isTracking = true;
    
    function initMap() {
        // Initialize map centered between pickup and delivery
        const centerLat = (pickupLocation.lat + deliveryLocation.lat) / 2;
        const centerLng = (pickupLocation.lng + deliveryLocation.lng) / 2;
        
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: centerLat, lng: centerLng },
            zoom: 12,
            zoomControl: true,
            mapTypeControl: true,
            scaleControl: true,
            streetViewControl: true,
            fullscreenControl: true,
        });
        
        // Initialize directions service
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true
        });
        
        // Add pickup marker
        pickupMarker = new google.maps.Marker({
            position: pickupLocation,
            map: map,
            title: "Pickup Location",
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png",
                scaledSize: new google.maps.Size(40, 40)
            },
            animation: google.maps.Animation.DROP
        });
        
        // Add info window for pickup
        const pickupInfo = new google.maps.InfoWindow({
            content: '<div class="text-center"><strong>Pickup Location</strong><br>Collect items here</div>'
        });
        pickupMarker.addListener('click', () => pickupInfo.open(map, pickupMarker));
        
        // Add delivery marker
        deliveryMarker = new google.maps.Marker({
            position: deliveryLocation,
            map: map,
            title: "Delivery Location",
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
                scaledSize: new google.maps.Size(40, 40)
            },
            animation: google.maps.Animation.DROP
        });
        
        // Add info window for delivery
        const deliveryInfo = new google.maps.InfoWindow({
            content: '<div class="text-center"><strong>Delivery Location</strong><br>Deliver items here</div>'
        });
        deliveryMarker.addListener('click', () => deliveryInfo.open(map, deliveryMarker));
        
        // Add rider marker
        riderMarker = new google.maps.Marker({
            position: riderLocation,
            map: map,
            title: "Your Location",
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
                scaledSize: new google.maps.Size(40, 40)
            },
            animation: google.maps.Animation.BOUNCE
        });
        
        // Stop bouncing after 2 seconds
        setTimeout(() => {
            riderMarker.setAnimation(null);
        }, 2000);
        
        // Get current location
        updateRiderLocation();
        
        // Start tracking every 30 seconds
        if (isTracking) {
            updateInterval = setInterval(updateRiderLocation, 30000);
        }
    }
    
    function updateRiderLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const newLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    riderLocation = newLocation;
                    riderMarker.setPosition(riderLocation);
                    
                    // Center map on rider's location if they are moving
                    if (map.getZoom() > 12) {
                        map.setCenter(riderLocation);
                    }
                    
                    // Update on server
                    updateLocationOnServer(riderLocation.lat, riderLocation.lng);
                    
                    // Update ETA if directions are active
                    if (directionsRenderer.getDirections()) {
                        calculateAndDisplayRoute();
                    }
                },
                function(error) {
                    console.log("Geolocation error:", error);
                    document.getElementById('liveIndicator').className = 'badge bg-danger';
                    document.getElementById('liveIndicator').innerHTML = '<i class="fas fa-circle fa-xs me-1"></i> Offline';
                }
            );
        }
    }
    
    function updateLocationOnServer(lat, lng) {
        fetch('{{ route("rider.tracking.update-location") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                order_id: {{ $order->id }},
                latitude: lat,
                longitude: lng
            })
        }).catch(error => console.log('Error updating location:', error));
    }
    
    function calculateAndDisplayRoute(destination = null) {
        const request = {
            origin: riderLocation,
            destination: destination || deliveryLocation,
            travelMode: google.maps.TravelMode.DRIVING,
            unitSystem: google.maps.UnitSystem.METRIC
        };
        
        directionsService.route(request, function(result, status) {
            if (status == 'OK') {
                directionsRenderer.setDirections(result);
                
                // Get duration
                const duration = result.routes[0].legs[0].duration.text;
                const distance = result.routes[0].legs[0].distance.text;
                
                // Show ETA notification
                showNotification(`🚗 ${distance} • ${duration} to destination`);
            }
        });
    }
    
    function centerMapOnPickup() {
        map.setCenter(pickupLocation);
        map.setZoom(15);
        calculateAndDisplayRoute(pickupLocation);
    }
    
    function centerMapOnDelivery() {
        map.setCenter(deliveryLocation);
        map.setZoom(15);
        calculateAndDisplayRoute(deliveryLocation);
    }
    
    function refreshLocation() {
        updateRiderLocation();
        showNotification('📍 Location updated!', 'info');
    }
    
    function showNotification(message, type = 'info') {
        // You can implement a toast notification here
        console.log(message);
    }
    
    function confirmDelivery() {
        event.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById('deliveryConfirmModal'));
        modal.show();
        
        document.getElementById('confirmDeliverBtn').onclick = function() {
            event.target.closest('form').submit();
        };
    }
    
    // Clean up interval when page unloads
    window.addEventListener('beforeunload', function() {
        if (updateInterval) {
            clearInterval(updateInterval);
        }
    });
    
    // Initialize map when page loads
    window.initMap = initMap;
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap&v=weekly" async defer></script>
@endpush
@endsection