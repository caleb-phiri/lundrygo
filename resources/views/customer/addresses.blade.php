@extends('layouts.app')

@section('title', 'My Addresses')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action">My Orders</a>
                <a href="{{ route('customer.orders.create') }}" class="list-group-item list-group-item-action">New Order</a>
                <a href="{{ route('customer.addresses') }}" class="list-group-item list-group-item-action active">Addresses</a>
                <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action">Profile</a>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Addresses</h4>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">+ Add New Address</button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(isset($addresses) && $addresses->count() > 0)
                        <div class="row">
                            @foreach($addresses as $address)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 {{ $address->is_default ? 'border-primary' : '' }}">
                                    <div class="card-body">
                                        <h6>
                                            {{ $address->label }} 
                                            @if($address->is_default)
                                                <span class="badge bg-primary">Default</span>
                                            @endif
                                        </h6>
                                        <p class="text-muted small mb-2">{{ $address->full_address }}</p>
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-phone me-1"></i> {{ $address->contact_phone ?? 'N/A' }}
                                        </p>
                                        @if($address->latitude && $address->longitude)
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-map-marker-alt me-1"></i> 
                                            <a href="https://www.google.com/maps?q={{ $address->latitude }},{{ $address->longitude }}" target="_blank" class="text-primary">
                                                View on Map
                                            </a>
                                        </p>
                                        @endif
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-outline-primary edit-address-btn" 
                                                    data-id="{{ $address->id }}" 
                                                    data-label="{{ $address->label }}" 
                                                    data-address="{{ $address->full_address }}" 
                                                    data-phone="{{ $address->contact_phone }}"
                                                    data-lat="{{ $address->latitude }}"
                                                    data-lng="{{ $address->longitude }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            @if(!$address->is_default)
                                                <form method="POST" action="{{ route('customer.addresses.default', $address->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-star"></i> Set Default
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('customer.addresses.delete', $address->id) }}" class="d-inline" onsubmit="return confirm('Delete this address?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No addresses saved yet.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">Add Your First Address</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('customer.addresses.store') }}" id="addAddressForm">
                @csrf
                <input type="hidden" name="latitude" id="addLatitude">
                <input type="hidden" name="longitude" id="addLongitude">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Label</label>
                                <select name="label" class="form-select" required>
                                    <option value="Home">🏠 Home</option>
                                    <option value="Work">💼 Work</option>
                                    <option value="Other">📍 Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Full Address <span class="text-danger">*</span></label>
                                <textarea name="full_address" id="addFullAddress" class="form-control" rows="3" required placeholder="Enter your full address (e.g., Street name, building name, area, city)"></textarea>
                                <small class="text-muted">Please type your complete address</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Phone</label>
                                <input type="text" name="contact_phone" id="addContactPhone" class="form-control" placeholder="Optional">
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="is_default" id="addIsDefault" class="form-check-input" value="1">
                                <label class="form-check-label">Set as default address</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pick Location on Map (Optional)</label>
                            <div id="addMapContainer" style="height: 350px; width: 100%; border-radius: 8px; margin-bottom: 10px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                <div class="text-center">
                                    <i class="fas fa-map-marked-alt fa-3x text-muted mb-2"></i>
                                    <p class="text-muted">Loading map...</p>
                                </div>
                            </div>
                            <div id="addMap" style="height: 350px; width: 100%; border-radius: 8px; margin-bottom: 10px; display: none;"></div>
                            <div class="d-grid gap-2 mt-2">
                                <button type="button" onclick="getCurrentLocationForAdd()" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-location-dot"></i> Use My Current Location
                                </button>
                            </div>
                            <div id="addCoordDisplay" class="small text-success mt-1" style="display: none;"></div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Click on map to save coordinates (optional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editAddressForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="latitude" id="editLatitude">
                <input type="hidden" name="longitude" id="editLongitude">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Label</label>
                                <select name="label" id="editLabel" class="form-select" required>
                                    <option value="Home">🏠 Home</option>
                                    <option value="Work">💼 Work</option>
                                    <option value="Other">📍 Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Full Address <span class="text-danger">*</span></label>
                                <textarea name="full_address" id="editFullAddress" class="form-control" rows="3" required></textarea>
                                <small class="text-muted">Please ensure your address is complete</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Phone</label>
                                <input type="text" name="contact_phone" id="editContactPhone" class="form-control">
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="is_default" id="editIsDefault" class="form-check-input" value="1">
                                <label class="form-check-label">Set as default address</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pick Location on Map (Optional)</label>
                            <div id="editMapContainer" style="height: 350px; width: 100%; border-radius: 8px; margin-bottom: 10px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                <div class="text-center">
                                    <i class="fas fa-map-marked-alt fa-3x text-muted mb-2"></i>
                                    <p class="text-muted">Loading map...</p>
                                </div>
                            </div>
                            <div id="editMap" style="height: 350px; width: 100%; border-radius: 8px; margin-bottom: 10px; display: none;"></div>
                            <div class="d-grid gap-2 mt-2">
                                <button type="button" onclick="getCurrentLocationForEdit()" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-location-dot"></i> Use My Current Location
                                </button>
                            </div>
                            <div id="editCoordDisplay" class="small text-success mt-1" style="display: none;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .modal-lg {
        max-width: 900px;
    }
    .card.border-primary {
        border: 2px solid #007bff !important;
    }
    .gm-style-iw {
        padding: 10px;
    }
    .error-border {
        border-color: #dc3545 !important;
        background-color: #fff0f0 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    let addMap, editMap;
    let addMarker, editMarker;
    let geocoder;
    
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.classList.add('error-border');
            field.focus();
            alert(message);
            setTimeout(() => {
                field.classList.remove('error-border');
            }, 3000);
        }
    }
    
    function clearError(fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.classList.remove('error-border');
        }
    }
    
    // Initialize Add Map
    function initAddMap() {
        const defaultLocation = { lat: -15.3875, lng: 28.3228 };
        
        try {
            addMap = new google.maps.Map(document.getElementById("addMap"), {
                center: defaultLocation,
                zoom: 14,
                mapTypeControl: true,
                streetViewControl: true,
                zoomControl: true,
            });
            
            geocoder = new google.maps.Geocoder();
            
            document.getElementById('addMapContainer').style.display = 'none';
            document.getElementById('addMap').style.display = 'block';
            
            addMap.addListener('click', function(event) {
                const lat = event.latLng.lat();
                const lng = event.latLng.lng();
                placeAddMarker(event.latLng);
                document.getElementById('addCoordDisplay').style.display = 'block';
                document.getElementById('addCoordDisplay').innerHTML = `<i class="fas fa-check-circle text-success"></i> Coordinates saved: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            });
        } catch (error) {
            console.error('Map error:', error);
            document.getElementById('addMapContainer').innerHTML = '<div class="text-center"><i class="fas fa-exclamation-triangle fa-3x text-danger mb-2"></i><p class="text-danger">Unable to load map. You can still add address manually.</p></div>';
        }
    }
    
    // Initialize Edit Map
    function initEditMap(lat, lng) {
        const location = { lat: parseFloat(lat), lng: parseFloat(lng) };
        
        try {
            editMap = new google.maps.Map(document.getElementById("editMap"), {
                center: location,
                zoom: 15,
                mapTypeControl: true,
                streetViewControl: true,
                zoomControl: true,
            });
            
            if (!geocoder) {
                geocoder = new google.maps.Geocoder();
            }
            
            document.getElementById('editMapContainer').style.display = 'none';
            document.getElementById('editMap').style.display = 'block';
            
            placeEditMarker(location);
            if (lat && lng && parseFloat(lat) !== 0) {
                document.getElementById('editCoordDisplay').style.display = 'block';
                document.getElementById('editCoordDisplay').innerHTML = `<i class="fas fa-check-circle text-success"></i> Current coordinates: ${lat}, ${lng}`;
            }
            
            editMap.addListener('click', function(event) {
                const newLat = event.latLng.lat();
                const newLng = event.latLng.lng();
                placeEditMarker(event.latLng);
                document.getElementById('editCoordDisplay').style.display = 'block';
                document.getElementById('editCoordDisplay').innerHTML = `<i class="fas fa-check-circle text-success"></i> Coordinates updated: ${newLat.toFixed(6)}, ${newLng.toFixed(6)}`;
            });
        } catch (error) {
            console.error('Map error:', error);
            document.getElementById('editMapContainer').innerHTML = '<div class="text-center"><i class="fas fa-exclamation-triangle fa-3x text-danger mb-2"></i><p class="text-danger">Unable to load map. You can still edit address manually.</p></div>';
        }
    }
    
    function placeAddMarker(location) {
        if (addMarker) {
            addMarker.setPosition(location);
        } else {
            addMarker = new google.maps.Marker({
                position: location,
                map: addMap,
                draggable: true,
                animation: google.maps.Animation.DROP
            });
            addMarker.addListener('dragend', function(event) {
                document.getElementById('addCoordDisplay').innerHTML = `<i class="fas fa-check-circle text-success"></i> Coordinates updated: ${event.latLng.lat().toFixed(6)}, ${event.latLng.lng().toFixed(6)}`;
            });
        }
        document.getElementById('addLatitude').value = location.lat();
        document.getElementById('addLongitude').value = location.lng();
    }
    
    function placeEditMarker(location) {
        if (editMarker) {
            editMarker.setPosition(location);
        } else {
            editMarker = new google.maps.Marker({
                position: location,
                map: editMap,
                draggable: true,
                animation: google.maps.Animation.DROP
            });
            editMarker.addListener('dragend', function(event) {
                document.getElementById('editCoordDisplay').innerHTML = `<i class="fas fa-check-circle text-success"></i> Coordinates updated: ${event.latLng.lat().toFixed(6)}, ${event.latLng.lng().toFixed(6)}`;
            });
        }
        document.getElementById('editLatitude').value = location.lat();
        document.getElementById('editLongitude').value = location.lng();
    }
    
    function getCurrentLocationForAdd() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const pos = { lat: position.coords.latitude, lng: position.coords.longitude };
                    addMap.setCenter(pos);
                    addMap.setZoom(15);
                    placeAddMarker(pos);
                    document.getElementById('addCoordDisplay').style.display = 'block';
                    document.getElementById('addCoordDisplay').innerHTML = `<i class="fas fa-check-circle text-success"></i> Your location coordinates saved: ${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}`;
                },
                function(error) {
                    alert('Unable to get your location. Please allow location access.');
                }
            );
        } else {
            alert('Geolocation is not supported by your browser.');
        }
    }
    
    function getCurrentLocationForEdit() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const pos = { lat: position.coords.latitude, lng: position.coords.longitude };
                    editMap.setCenter(pos);
                    editMap.setZoom(15);
                    placeEditMarker(pos);
                    document.getElementById('editCoordDisplay').style.display = 'block';
                    document.getElementById('editCoordDisplay').innerHTML = `<i class="fas fa-check-circle text-success"></i> Your location coordinates saved: ${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}`;
                },
                function(error) {
                    alert('Unable to get your location. Please allow location access.');
                }
            );
        } else {
            alert('Geolocation is not supported by your browser.');
        }
    }
    
    // Edit button handler
    document.querySelectorAll('.edit-address-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const label = this.dataset.label;
            let address = this.dataset.address;
            const phone = this.dataset.phone;
            const lat = this.dataset.lat;
            const lng = this.dataset.lng;
            
            // Ensure address is not empty
            if (!address || address === '' || address === 'null' || address === 'undefined') {
                address = '';
            }
            
            document.getElementById('editLabel').value = label || 'Home';
            document.getElementById('editFullAddress').value = address;
            document.getElementById('editContactPhone').value = phone || '';
            document.getElementById('editIsDefault').checked = false;
            document.getElementById('editLatitude').value = lat || '';
            document.getElementById('editLongitude').value = lng || '';
            
            document.getElementById('editAddressForm').action = '/customer/addresses/' + id;
            
            // Reset map container
            document.getElementById('editMapContainer').style.display = 'flex';
            document.getElementById('editMap').style.display = 'none';
            document.getElementById('editMapContainer').innerHTML = '<div class="text-center"><i class="fas fa-map-marked-alt fa-3x text-muted mb-2"></i><p class="text-muted">Loading map...</p></div>';
            document.getElementById('editCoordDisplay').style.display = 'none';
            
            if (lat && lng && parseFloat(lat) !== 0) {
                setTimeout(() => initEditMap(lat, lng), 300);
            } else {
                setTimeout(() => initEditMap(-15.3875, 28.3228), 300);
            }
            
            new bootstrap.Modal(document.getElementById('editAddressModal')).show();
        });
    });
    
    // Validate and submit Edit Form
    document.querySelector('#editAddressForm button[type="submit"]')?.addEventListener('click', function(e) {
        e.preventDefault();
        
        const addressField = document.getElementById('editFullAddress');
        const addressValue = addressField.value.trim();
        
        if (!addressValue || addressValue === '') {
            showError('editFullAddress', 'Please enter your full address before saving.');
            return false;
        }
        
        // Clear error if exists
        clearError('editFullAddress');
        
        // Submit the form
        document.getElementById('editAddressForm').submit();
    });
    
    // Validate and submit Add Form
    document.querySelector('#addAddressForm button[type="submit"]')?.addEventListener('click', function(e) {
        const addressField = document.getElementById('addFullAddress');
        const addressValue = addressField.value.trim();
        
        if (!addressValue || addressValue === '') {
            e.preventDefault();
            showError('addFullAddress', 'Please enter your full address before saving.');
            return false;
        }
        
        clearError('addFullAddress');
    });
    
    // Clear error when user starts typing
    document.getElementById('editFullAddress')?.addEventListener('input', function() {
        clearError('editFullAddress');
    });
    
    document.getElementById('addFullAddress')?.addEventListener('input', function() {
        clearError('addFullAddress');
    });
    
    // Reset add modal when closed
    document.getElementById('addAddressModal')?.addEventListener('hidden.bs.modal', function() {
        document.getElementById('addFullAddress').value = '';
        document.getElementById('addContactPhone').value = '';
        document.getElementById('addIsDefault').checked = false;
        document.getElementById('addLatitude').value = '';
        document.getElementById('addLongitude').value = '';
        document.getElementById('addCoordDisplay').style.display = 'none';
        clearError('addFullAddress');
        
        if (addMarker) {
            addMarker.setMap(null);
            addMarker = null;
        }
        
        document.getElementById('addMapContainer').style.display = 'flex';
        document.getElementById('addMap').style.display = 'none';
        document.getElementById('addMapContainer').innerHTML = '<div class="text-center"><i class="fas fa-map-marked-alt fa-3x text-muted mb-2"></i><p class="text-muted">Loading map...</p></div>';
    });
    
    // Reset edit modal when closed
    document.getElementById('editAddressModal')?.addEventListener('hidden.bs.modal', function() {
        clearError('editFullAddress');
        if (editMarker) {
            editMarker.setMap(null);
            editMarker = null;
        }
    });
    
    // Load Google Maps
    function loadGoogleMapsAPI() {
        const apiKey = '{{ env("GOOGLE_MAPS_API_KEY") }}';
        if (apiKey && apiKey !== '') {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initAddMap&libraries=places`;
            script.async = true;
            script.defer = true;
            script.onerror = function() {
                document.getElementById('addMapContainer').innerHTML = '<div class="text-center"><i class="fas fa-exclamation-triangle fa-3x text-danger mb-2"></i><p class="text-danger">Failed to load Google Maps. Please enter address manually.</p></div>';
            };
            document.head.appendChild(script);
        } else {
            document.getElementById('addMapContainer').innerHTML = '<div class="text-center"><i class="fas fa-exclamation-triangle fa-3x text-danger mb-2"></i><p class="text-danger">Google Maps API key is missing. You can still add address manually.</p></div>';
        }
    }
    
    loadGoogleMapsAPI();
    
    window.getCurrentLocationForAdd = getCurrentLocationForAdd;
    window.getCurrentLocationForEdit = getCurrentLocationForEdit;
    window.initAddMap = initAddMap;
</script>
@endpush
@endsection